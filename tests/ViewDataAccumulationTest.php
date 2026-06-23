<?php

namespace Webwizo\Shortcodes;

use ReflectionProperty;
use Webwizo\Shortcodes\Compilers\ShortcodeCompiler;

/**
 * Regression coverage for the view-data lifecycle on the shared singleton compiler.
 *
 * The compiler is bound as a singleton, and {@see View::renderContents()} merges the
 * current view's data into it on every render. Without a reset this grows without
 * bound across a request. PR #88 introduced the merge to keep parent/layout data
 * available to shortcode callbacks while nested views render; that behaviour must be
 * preserved while independent, sequential top-level renders no longer accumulate.
 *
 * @see https://github.com/webwizo/laravel-shortcodes/pull/88
 */
class ViewDataAccumulationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        view()->addNamespace('ViewData', __DIR__ . '/views');
    }

    /**
     * Read the compiler's accumulated `_viewData` array via reflection.
     *
     * @return array<string, mixed>
     */
    private function viewDataKeys(ShortcodeCompiler $compiler): array
    {
        $property = new ReflectionProperty(ShortcodeCompiler::class, '_viewData');
        $property->setAccessible(true);

        return $property->getValue($compiler) ?? [];
    }

    /**
     * Build a data array of $count distinct keys, namespaced per render.
     *
     * @return array<string, string>
     */
    private function renderData(int $render, int $count = 25): array
    {
        $data = [];
        for ($key = 0; $key < $count; $key++) {
            $data["render_{$render}_field_{$key}"] = 'value';
        }

        return $data;
    }

    public function test_view_data_does_not_accumulate_across_sequential_top_level_renders(): void
    {
        $compiler = app('shortcode.compiler');
        $compiler->add('probe', function () {
            return '';
        });

        $renders = 50;
        $keysPerRender = 25;

        for ($render = 1; $render <= $renders; $render++) {
            view('ViewData::viewdata-top-level', $this->renderData($render, $keysPerRender))
                ->withShortcodes()
                ->render();
        }

        // Without the top-level reset this would be $keysPerRender * $renders (1250);
        // with the reset only the final render's data remains on the singleton.
        $this->assertCount(
            $keysPerRender,
            $this->viewDataKeys($compiler),
            'View data must not accumulate across independent top-level renders.'
        );
    }

    public function test_nested_view_render_still_merges_parent_data(): void
    {
        $compiler = app('shortcode.compiler');

        $captured = null;

        // Outer shortcode renders an inner view while the outer view is still in
        // progress (a nested render), passing additional data of its own.
        $compiler->add('renderNested', function () {
            return view('ViewData::viewdata-nested-inner', ['child' => 'partial'])
                ->withShortcodes()
                ->render();
        });

        // Inner shortcode captures the view data it receives during the nested render.
        $compiler->add('mergedKeys', function ($shortcode, $content, $shortcodeCompiler, $name, $viewData) use (&$captured) {
            $captured = $viewData;

            return '';
        });

        view('ViewData::viewdata-nested-outer', ['parent' => 'layout'])
            ->withShortcodes()
            ->render();

        // The nested render must see both the parent (outer) and child (inner) data,
        // proving the merge introduced in PR #88 is preserved.
        $this->assertIsArray($captured);
        $this->assertSame('layout', $captured['parent'] ?? null);
        $this->assertSame('partial', $captured['child'] ?? null);
    }
}
