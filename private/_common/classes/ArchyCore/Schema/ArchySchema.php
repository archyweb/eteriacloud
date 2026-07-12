<?php

require_once __DIR__.'/AliasResolver.php';
require_once __DIR__.'/AliasDictionary.php';
require_once __DIR__.'/IntentLoader.php';
require_once __DIR__.'/Field.php';
require_once __DIR__.'/FieldCollection.php';
require_once __DIR__.'/ValueIndex.php';
require_once __DIR__.'/../../ArchySearch/Semantic/Dictionaries/Synonyms.php';

class ArchySchema
{
    protected $intentName;
    protected $intent;
    protected $fields;
	protected $aliases;
	protected $valueIndex;

    public function __construct($intentName, $intent)
    {
        $this->intentName = $intentName;
        $this->intent = $intent;
        $this->fields = new FieldCollection();
		$this->aliases = new AliasResolver();
		$this->valueIndex = new ValueIndex();

        $this->build();
    }

    public static function load($intentName)
    {
        $intent = IntentLoader::load($intentName);

        return new self($intentName, $intent);
    }

    protected function build()
    {
        if (!isset($this->intent->columns) || !is_array($this->intent->columns)) {
            return;
        }

        foreach ($this->intent->columns as $columnSql => $config) {

            $field = new Field(array(
				'intent'        => $this->intentName,
				'name'          => $this->normalizeFieldName($columnSql),
				'column'        => $columnSql,
				'label'         => $config['label'] ?? '',
				'type'          => $config['type'] ?? 'text',
				'search_type'   => $config['search_type'] ?? '',
				'search_db'     => $config['search_db'] ?? '',
				'tag_category'  => $config['tag_category'] ?? '',
				'lookup_text'   => $config['lookup_text'] ?? '',
				'intent_ref'    => $config['intent'] ?? '',
				'raw'           => $config
			));

            $this->fields->add($field);
			$this->aliases->register($field->name(), $field->name());
			$this->aliases->register($field->label(), $field->name());

			$searchAliases = $field->config('search_alias');
			if (is_array($searchAliases)) {
				foreach ($searchAliases as $alias) {
					$this->aliases->register($alias, $field->name());
				}
			}
			$synonyms = Synonyms::all();

			if (isset($synonyms[$field->name()]) && is_array($synonyms[$field->name()])) {
				foreach ($synonyms[$field->name()] as $alias) {
					$this->aliases->register($alias, $field->name());
				}
			}
			$dictionary = AliasDictionary::forIntent($this->intentName);

			if (isset($dictionary[$field->name()]) && is_array($dictionary[$field->name()])) {
				foreach ($dictionary[$field->name()] as $alias) {
					$this->aliases->register($alias, $field->name());
				}
			}
        }
    }
	

    public function field($name)
	{
		$fieldName = $this->aliases->resolve($name);

		if ($fieldName) {
			return $this->fields->get($fieldName);
		}

		return $this->fields->get($name);
	}
	public function aliases()
	{
		return $this->aliases->all();
	}

    public function fields()
    {
        return $this->fields;
    }

    public function hasField($name)
    {
        return $this->fields->has($name);
    }

    protected function normalizeFieldName($columnSql)
    {
        $columnSql = str_replace('`','',$columnSql);

        if (strpos($columnSql,'.') !== false) {
            $parts = explode('.',$columnSql);
            return trim(end($parts));
        }

        return trim($columnSql);
    }
	
		public function valueIndex()
	{
		return $this->valueIndex;
	}

	public function findValuesInText($text)
	{
		return $this->valueIndex->findInText($text);
	}

}