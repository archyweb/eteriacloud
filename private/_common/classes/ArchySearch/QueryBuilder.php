<?php

require_once __DIR__ . '/Query.php';

require_once __DIR__ . '/Expressions/EmptyExpression.php';
require_once __DIR__ . '/Expressions/NotEmptyExpression.php';
require_once __DIR__ . '/Expressions/ComparisonExpression.php';
require_once __DIR__ . '/Expressions/RangeExpression.php';
require_once __DIR__ . '/Expressions/ExactExpression.php';
require_once __DIR__ . '/Expressions/TokensExpression.php';
require_once __DIR__ . '/Expressions/FieldExpression.php';

class ArchySearchQueryBuilder
{
    public static function buildColumnWhere(SearchQuery $query, $columnSql, &$bindings, $mode = 'column')
    {
        if ($query->isEmpty()) {
            return false;
        }

        $conditions = array();

        foreach ($query->expressions() as $expression) {
			
			if ($expression instanceof FieldExpression) {
				$expression = $expression->expression();
			}

            if ($expression instanceof EmptyExpression) {
                $conditions[] = self::buildEmpty($columnSql);
                continue;
            }

            if ($expression instanceof NotEmptyExpression) {
                $conditions[] = self::buildNotEmpty($columnSql);
                continue;
            }

            if ($expression instanceof ComparisonExpression) {
                $conditions[] = self::buildComparison($columnSql, $expression);
                continue;
            }

            if ($expression instanceof RangeExpression) {
                $conditions[] = self::buildRange($columnSql, $expression);
                continue;
            }

            if ($expression instanceof ExactExpression) {
                $conditions[] = self::buildExact($columnSql, $expression, $bindings, $mode);
                continue;
            }

            if ($expression instanceof TokensExpression) {
                $conditions[] = self::buildTokens($columnSql, $expression, $bindings);
                continue;
            }
        }

        $conditions = array_filter($conditions);

        if (empty($conditions)) {
            return false;
        }

        return '('.implode(' AND ', $conditions).')';
    }

    protected static function buildEmpty($columnSql)
    {
        $cleanColumnSql = self::cleanColumnSql($columnSql);

        return "(
            ".$columnSql." IS NULL
            OR ".$cleanColumnSql." = ''
            OR ".$columnSql." = '0'
            OR ".$columnSql." = '[]'
            OR ".$columnSql." = 'a:0:{}'
        )";
    }

    protected static function buildNotEmpty($columnSql)
    {
        $cleanColumnSql = self::cleanColumnSql($columnSql);

        return "(
            ".$columnSql." IS NOT NULL
            AND ".$cleanColumnSql." <> ''
            AND ".$columnSql." <> '0'
            AND ".$columnSql." <> '[]'
            AND ".$columnSql." <> 'a:0:{}'
        )";
    }

    protected static function buildComparison($columnSql, ComparisonExpression $expression)
    {
        return "(
            ".$columnSql." IS NOT NULL
            AND TRIM(".$columnSql.") <> ''
            AND CAST(".$columnSql." AS DECIMAL(20,4)) ".$expression->operator()." ".floatval($expression->value())."
        )";
    }

    protected static function buildRange($columnSql, RangeExpression $expression)
    {
        return "(
            ".$columnSql." IS NOT NULL
            AND TRIM(".$columnSql.") <> ''
            AND ".$columnSql." <> '0'
            AND CAST(".$columnSql." AS DECIMAL(20,4)) BETWEEN ".floatval($expression->from())." AND ".floatval($expression->to())."
        )";
    }

    protected static function buildExact($columnSql, ExactExpression $expression, &$bindings, $mode)
    {
        $phrase = trim($expression->value());

        if ($phrase === '') {
            return false;
        }

        if ($mode === 'global') {
            $phraseRegex = preg_quote(strtolower($phrase), '/');

            $bindingRegex = SSP::bind(
                $bindings,
                '(^|[^[:alnum:]])'.$phraseRegex.'([^[:alnum:]]|$)',
                PDO::PARAM_STR
            );

            return "LOWER(TRIM(CAST(".$columnSql." AS CHAR))) REGEXP ".$bindingRegex;
        }

        $binding = SSP::bind(
            $bindings,
            strtolower($phrase),
            PDO::PARAM_STR
        );

        return "LOWER(TRIM(".$columnSql.")) = ".$binding;
    }

    protected static function buildTokens($columnSql, TokensExpression $expression, &$bindings)
    {
        $conditions = array();

        foreach ($expression->values() as $token) {
            $token = trim($token);

            if ($token === '') {
                continue;
            }

            $binding = SSP::bind(
                $bindings,
                '%'.$token.'%',
                PDO::PARAM_STR
            );

            $conditions[] = $columnSql." LIKE ".$binding;
        }

        if (empty($conditions)) {
            return false;
        }

        return "(".implode(" AND ", $conditions).")";
    }

    protected static function cleanColumnSql($columnSql)
    {
        return "TRIM(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(".$columnSql.", '&nbsp;', ''),
                    '<p></p>', ''),
                '<br>', ''),
            '<br />', '')
        )";
    }
}