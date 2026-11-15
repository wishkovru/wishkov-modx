<?php
/**
 * Wishkov button snippet: settings sanitizing and markup. No MODX dependency.
 *
 * @copyright  Wishkov, https://wishkov.ru
 * @license    GNU General Public License version 2 or later
 */

namespace Wishkov\Modx;

use Wishkov\WishkovButton;

final class Snippet
{
    private const TITLE_LIMIT = 200;
    private const LABEL_LIMIT = 60;

    public static function defaults(): array
    {
        return [
            'theme' => 'fill',
            'size' => 'm',
            'label' => WishkovButton::DEFAULT_LABEL,
            'icon_only' => false,
            'margin' => 8,
        ];
    }

    /** The snippet property wins when it is set and not an empty string, then the system setting, then the default. */
    private static function pick(array $props, string $propKey, array $system, string $sysKey, $default)
    {
        if (isset($props[$propKey]) && $props[$propKey] !== '') {
            return $props[$propKey];
        }
        if (isset($system[$sysKey]) && $system[$sysKey] !== '') {
            return $system[$sysKey];
        }
        return $default;
    }

    /**
     * @param array $props  snippet properties: theme, size, label, iconOnly, margin
     * @param array $system system settings without the namespace: theme, size, label, icon_only, margin
     */
    public static function settings(array $props, array $system): array
    {
        $d = self::defaults();

        $theme = (string) self::pick($props, 'theme', $system, 'theme', $d['theme']);
        if (!array_key_exists($theme, WishkovButton::themes())) {
            $theme = $d['theme'];
        }
        $size = (string) self::pick($props, 'size', $system, 'size', $d['size']);
        if (!array_key_exists($size, WishkovButton::sizes())) {
            $size = $d['size'];
        }
        $label = trim((string) self::pick($props, 'label', $system, 'label', $d['label']));
        $label = mb_substr($label, 0, self::LABEL_LIMIT);
        if ($label === '') {
            $label = $d['label'];
        }
        $iconRaw = self::pick($props, 'iconOnly', $system, 'icon_only', '0');
        $iconOnly = $iconRaw === true || $iconRaw === 1 || (is_string($iconRaw) && in_array(strtolower(trim($iconRaw)), ['1', 'true', 'on', 'yes'], true));
        $marginRaw = self::pick($props, 'margin', $system, 'margin', $d['margin']);
        $margin = is_numeric($marginRaw) ? (int) $marginRaw : $d['margin'];
        $margin = max(0, min(40, $margin));

        return [
            'theme' => $theme,
            'size' => $size,
            'label' => $label,
            'icon_only' => $iconOnly,
            'margin' => $margin,
        ];
    }

    private static function cutTitle(string $title): string
    {
        return WishkovButton::encode(mb_substr($title, 0, self::TITLE_LIMIT));
    }

    /** Unescaped button address. */
    public static function href(string $url, string $title, string $ref = 'modx'): string
    {
        $href = WishkovButton::href($url, $ref);
        return $title === '' ? $href : $href . '&title=' . self::cutTitle($title);
    }

    public static function html(array $settings, string $url, string $title, string $ref = 'modx'): string
    {
        $html = WishkovButton::render($url, [
            'label' => $settings['label'],
            'size' => $settings['size'],
            'theme' => $settings['theme'],
            'icon_only' => $settings['icon_only'],
            'ref' => $ref,
        ]);
        // href() is the single source of the address: replace the whole leading href value with it.
        $count = 0;
        $result = preg_replace_callback(
            '/^<a href="[^"]*"/',
            static function () use ($url, $title, $ref): string {
                return '<a href="' . self::escapeAttr(self::href($url, $title, $ref)) . '"';
            },
            $html,
            1,
            $count
        );
        if ($result === null || $count !== 1) {
            throw new \RuntimeException('Wishkov button markup changed: leading href attribute not found.');
        }
        return '<div class="wishkov-button" style="margin:' . (int) $settings['margin'] . 'px 0">' . $result . '</div>';
    }

    /** Same escaping as the button class: & < > " '. */
    private static function escapeAttr(string $text): string
    {
        return str_replace(['&', '<', '>', '"', "'"], ['&amp;', '&lt;', '&gt;', '&quot;', '&#x27;'], $text);
    }
}
