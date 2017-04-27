<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(
        [
            'public',
            'storage',
            'vendor',
            'node_modules',
        ]
    )
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules(
        [
            '@PSR2' => true,
            'array_syntax' => ['syntax' => 'short'],
            'blank_line_after_opening_tag' => true,
            'blank_line_before_return' => true,
            'braces' => ['allow_single_line_closure' => true],
            'cast_spaces' => true,
            'combine_consecutive_unsets' => true,
            'concat_space' => ['spacing' => 'one'],
            'elseif' => true,
            'full_opening_tag' => true,
            'function_typehint_space' => true,
            'linebreak_after_opening_tag' => true,
            'native_function_invocation' => true,
            'no_blank_lines_after_phpdoc' => true,
            'no_closing_tag' => true,
            'no_empty_comment' => true,
            'no_empty_phpdoc' => true,
            'no_extra_consecutive_blank_lines' => true,
            'no_php4_constructor' => true,
            'no_short_echo_tag' => true,
            'no_spaces_after_function_name' => true,
            'no_trailing_comma_in_singleline_array' => true,
            'no_trailing_whitespace' => true,
            'no_trailing_whitespace_in_comment' => true,
            'no_unreachable_default_argument_value' => true,
            'no_useless_else' => true,
            'no_useless_return' => true,
            'no_whitespace_before_comma_in_array' => true,
            'no_whitespace_in_blank_line' => true,
            'non_printable_character' => true,
            'normalize_index_brace' => true,
            'not_operator_with_successor_space' => true,
            'object_operator_without_whitespace' => true,
            'ordered_class_elements' => true,
            'ordered_imports' => true,
            'php_unit_construct' => true,
            'php_unit_dedicate_assert' => true,
            'phpdoc_add_missing_param_annotation' => true,
            'phpdoc_align' => true,
            'phpdoc_indent' => true,
            'phpdoc_inline_tag' => true,
            'phpdoc_no_access' => true,
            // 'phpdoc_no_empty_return' => true,
            'phpdoc_no_package' => true,
            'phpdoc_no_useless_inheritdoc' => true,
            'phpdoc_order' => true,
            'phpdoc_return_self_reference' => true,
            'phpdoc_scalar' => true,
            'phpdoc_separation' => true,
            'phpdoc_single_line_var_spacing' => true,
            'phpdoc_summary' => true,
            'phpdoc_trim' => true,
            'phpdoc_types' => true,
            'pow_to_exponentiation' => true,
            'semicolon_after_instruction' => true,
            'single_blank_line_before_namespace' => true,
            'ternary_operator_spaces' => true,
            'trailing_comma_in_multiline_array' => true,
            'trim_array_spaces' => true,
            'unary_operator_spaces' => true,
        ]
    )
    ->setFinder($finder);
