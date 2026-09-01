<?php

namespace Webwizo\Shortcodes;

use ReflectionClass;
use Webwizo\Shortcodes\Compilers\ShortcodeCompiler;

/**
 * Regression coverage for attribute parsing after HTML sanitizer numeric entities.
 *
 * Sanitizers may encode `=` and `"` as `&#61;` and `&#34;`. Only `htmlspecialchars_decode()`
 * is insufficient; `html_entity_decode()` is required so attributes parse correctly.
 */
class HtmlEntityAttributeParsingTest extends TestCase
{
    public function test_parse_attributes_decodes_numeric_entities_from_html_sanitizers(): void
    {
        $compiler = new ShortcodeCompiler();
        $method = (new ReflectionClass($compiler))->getMethod('parseAttributes');
        $method->setAccessible(true);

        $attributes = $method->invoke(
            $compiler,
            ' href&#61;&#34;https://yahoo.com&#34; class&#61;&#34;btn-secondary&#34;'
        );

        $this->assertSame([
            'href' => 'https://yahoo.com',
            'class' => 'btn-secondary',
        ], $attributes);
    }

    public function test_shortcode_compile_parses_attributes_after_html_sanitizer_numeric_entities(): void
    {
        $captured = null;

        $this->shortcode->register('link', function ($shortcode) use (&$captured) {
            $captured = $shortcode->toArray();

            return 'ok';
        });

        $this->shortcode->compile(
            '[link href&#61;&#34;https://yahoo.com&#34; class&#61;&#34;btn-secondary&#34;][/link]'
        );

        $this->assertNotNull($captured);
        $this->assertSame('https://yahoo.com', $captured['href']);
        $this->assertSame('btn-secondary', $captured['class']);
    }
}
