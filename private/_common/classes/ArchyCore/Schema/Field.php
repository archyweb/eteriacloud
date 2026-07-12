<?php

class Field
{
    protected $intent;
    protected $name;
    protected $column;
    protected $label;
    protected $type;
    protected $searchType;
	protected $searchDb;
    protected $tagCategory;
    protected $lookupText;
    protected $intentRef;
    protected $config;

    public function __construct(array $config)
    {
        $this->intent      = $config['intent'] ?? '';
        $this->name        = $config['name'] ?? '';
        $this->column      = $config['column'] ?? '';
        $this->label       = $config['label'] ?? '';
        $this->type        = $config['type'] ?? 'text';
        $this->searchType  = $config['search_type'] ?? '';
		$this->searchDb    = $config['search_db'] ?? '';
        $this->tagCategory = $config['tag_category'] ?? '';
        $this->lookupText  = $config['lookup_text'] ?? '';
        $this->intentRef   = $config['intent_ref'] ?? '';
        $this->config      = $config['raw'] ?? array();
    }

    public function intent()      { return $this->intent; }
    public function name()        { return $this->name; }
    public function column()      { return $this->column; }
    public function label()       { return $this->label; }
    public function type()        { return $this->type; }
    public function searchType()  { return $this->searchType; }
	public function searchDb()    { return $this->searchDb; }
    public function tagCategory() { return $this->tagCategory; }
    public function lookupText()  { return $this->lookupText; }
    public function intentRef()   { return $this->intentRef; }

    public function config($key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return $this->config[$key] ?? null;
    }

    public function toArray()
    {
        return array(
            'intent'        => $this->intent,
            'name'          => $this->name,
            'column'        => $this->column,
            'label'         => $this->label,
            'type'          => $this->type,
            'search_type'   => $this->searchType,
			'search_db'     => $this->searchDb,
            'tag_category'  => $this->tagCategory,
            'lookup_text'   => $this->lookupText,
            'intent_ref'    => $this->intentRef
        );
    }
}