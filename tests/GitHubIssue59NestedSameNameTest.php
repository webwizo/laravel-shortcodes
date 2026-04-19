<?php

namespace Webwizo\Shortcodes;

/**
 * GitHub #59 — nested shortcodes that share the same tag name.
 *
 * @see https://github.com/webwizo/laravel-shortcodes/issues/59
 */
class GitHubIssue59NestedSameNameTest extends TestCase
{
    public function test_nested_div_shortcodes_produce_correctly_nested_markup(): void
    {
        $this->shortcode->register('div', function ($shortcode, $content) {
            $class = $shortcode->class ? ' class="' . e($shortcode->class) . '"' : '';

            return '<div' . $class . '>' . $content . '</div>';
        });

        $out = $this->shortcode->compile(
            '[div class="row"][div class="col-md-6"]Left[/div][div class="col-md-6"]Right[/div][/div]'
        );

        $this->assertStringContainsString('<div class="row">', $out);
        $this->assertStringContainsString('<div class="col-md-6">Left</div>', $out);
        $this->assertStringContainsString('<div class="col-md-6">Right</div>', $out);
        $this->assertStringEndsWith('</div>', $out);
    }
}
