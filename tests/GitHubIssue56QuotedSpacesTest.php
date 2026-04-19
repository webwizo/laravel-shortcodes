<?php

namespace Webwizo\Shortcodes;

/**
 * GitHub #56 — attribute values with spaces inside single-quoted values must stay one string.
 *
 * @see https://github.com/webwizo/laravel-shortcodes/issues/56
 */
class GitHubIssue56QuotedSpacesTest extends TestCase
{
    public function test_single_quoted_attribute_value_with_spaces_is_not_split_into_numeric_keys(): void
    {
        $captured = null;

        $this->shortcode->register('pdfmodal', function ($shortcode) use (&$captured) {
            $captured = $shortcode->toArray();

            return 'ok';
        });

        $this->shortcode->compile(
            "[pdfmodal path=/files/docs.pdf modaltitle='Flat Rate Cost']Click Here[/pdfmodal]"
        );

        $this->assertNotNull($captured);
        $this->assertArrayHasKey('modaltitle', $captured);
        $this->assertSame('Flat Rate Cost', $captured['modaltitle']);
        $this->assertArrayNotHasKey(0, $captured, 'Spaces must not create numeric-indexed attribute fragments');
    }

    public function test_double_quoted_value_with_spaces_before_closing_bracket(): void
    {
        $captured = null;

        $this->shortcode->register('widget', function ($shortcode) use (&$captured) {
            $captured = $shortcode->toArray();

            return 'x';
        });

        $this->shortcode->compile('[widget title="One Two Three"][/widget]');

        $this->assertSame('One Two Three', $captured['title']);
    }

    public function test_parse_attributes_allows_closing_bracket_immediately_after_quoted_value(): void
    {
        $compiler = $this->app->make('shortcode.compiler');
        $method = (new \ReflectionClass($compiler))->getMethod('parseAttributes');
        $method->setAccessible(true);

        // Artificial input may leave `]` for a follow-up token; real shortcode tags never pass `]` here.
        $attrs = $method->invoke($compiler, ' modaltitle="Flat Rate Cost"]');

        $this->assertSame('Flat Rate Cost', $attrs['modaltitle']);
    }

    public function test_hyphenated_attribute_names_are_recognized(): void
    {
        $captured = null;

        $this->shortcode->register('box', function ($shortcode) use (&$captured) {
            $captured = $shortcode->toArray();

            return 'x';
        });

        $this->shortcode->compile('[box data-size="large"][/box]');

        $this->assertSame('large', $captured['data-size']);
    }
}
