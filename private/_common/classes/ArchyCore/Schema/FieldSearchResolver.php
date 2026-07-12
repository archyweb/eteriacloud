<?php

require_once __DIR__.'/Field.php';

class FieldSearchResolver
{
    public static function build(Field $field, $expression, &$bindings)
    {
        $columnSql = $field->searchDb() ?: $field->column();

        if ($field->searchType() === 'tags') {
            return self::buildTags($field, $expression, $bindings);
        }

        return ArchySearchQueryBuilder::buildColumnWhere(
            self::wrapExpression($expression),
            $columnSql,
            $bindings,
            'column'
        );
    }

    protected static function wrapExpression($expression)
    {
        $query = new SearchQuery('simple');
        $query->add($expression);
        return $query;
    }

protected static function buildTags(Field $field, $expression, &$bindings)
{
    $columnSql = $field->column();

    if ($expression instanceof EmptyExpression) {
        return "(
            ".$columnSql." IS NULL
            OR TRIM(".$columnSql.") = ''
            OR ".$columnSql." = '0'
            OR ".$columnSql." = '[]'
            OR ".$columnSql." = 'a:0:{}'
        )";
    }

    if ($expression instanceof NotEmptyExpression) {
        return "(
            ".$columnSql." IS NOT NULL
            AND TRIM(".$columnSql.") <> ''
            AND ".$columnSql." <> '0'
            AND ".$columnSql." <> '[]'
            AND ".$columnSql." <> 'a:0:{}'
        )";
    }

    if (!method_exists($expression, 'values')) {
        return false;
    }

    $tagCategory = $field->tagCategory();
    $values = $expression->values();
    $conditions = array();

    foreach ($values as $token) {
        $token = trim($token);

        if ($token === '') {
            continue;
        }

        if (mb_strtolower($token, 'UTF-8') === mb_strtolower($field->label(), 'UTF-8')) {
            continue;
        }

        $bindingTagToken = SSP::bind($bindings, '%'.$token.'%', PDO::PARAM_STR);
        $bindingCategory = SSP::bind($bindings, $tagCategory, PDO::PARAM_STR);

        $conditions[] = "EXISTS (
            SELECT 1
            FROM archy_tag atg
            WHERE atg.status = 1
            AND atg.categoria = ".$bindingCategory."
            AND atg.tag LIKE ".$bindingTagToken."
            AND (
                ".$columnSql." = CAST(atg.id AS CHAR)
                OR ".$columnSql." LIKE CONCAT('%,', atg.id, ',%')
                OR ".$columnSql." LIKE CONCAT(atg.id, ',%')
                OR ".$columnSql." LIKE CONCAT('%,', atg.id)
                OR ".$columnSql." LIKE CONCAT('%\"', atg.id, '\"%')
            )
        )";
    }

    return !empty($conditions) ? '('.implode(' AND ', $conditions).')' : false;
}
}