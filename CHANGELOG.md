# Changelog

All Notable changes to `laravel-shortcodes` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://github.com/webwizo/laravel-shortcodes) principles.

## 2026-06-24

#### Shortcodes Latest version: v1.0.33

### Fixed
- Fix unbounded growth of `ShortcodeCompiler::$_viewData` on the shared `shortcode.compiler` singleton. Since v1.0.32 (`#88`), view data merged on every `View::renderContents()` call was never released, causing `$_viewData` to grow linearly across independent, sequential top-level renders within a request (memory pressure on pages rendering many views). The fix resets `$_viewData` only at the top-level render boundary (`Factory::doneRendering()`), so nested-view data merging introduced in `#88` is preserved while data from unrelated top-level renders no longer accumulates (`#89`).

### Tests
- Add package regression test `ViewDataAccumulationTest` verifying `$_viewData` does not accumulate across independent top-level renders, while nested renders still see merged parent data.

## 2026-04-22

#### Shortcodes Latest version: v1.0.31

### Added
- Add `Mailable::withShortcodes()` support so shortcode compilation can be enabled directly for mailable views (`#52`).

### Tests
- Add package regression test `GitHubIssue52MailableShortcodesTest` to verify mailables render shortcode tags when `->withShortcodes()` is used.

## 2026-04-20

#### Shortcodes Latest version: v1.0.30

### Fixed
- Fix escaped shortcode handling so non-shortcode `[[` sequences are preserved instead of globally rewritten. This prevents Livewire `wire:snapshot` JSON corruption introduced in `v1.0.29` (`#87`).

### Tests
- Add package regression test `GitHubIssue87LivewireSnapshotEscapeTest` to verify snapshot-like JSON with `[[],{"s":"arr"}]` is not modified while escaped shortcode syntax still unescapes correctly.

## 2016-12-06

#### Shortcodes Latest version: v1.0.6

### Added
- Add Laravel versions from ~5.1 to ~5.3

## 2016-08-25

#### Shortcodes Latest version: v1.0.5

### Added
- Update dependencies for Laravel 5.3 


## 2016-05-26

### Added
- Strip shortcodes


## 2016-05-24

### Added
- Compile Shortcodes


