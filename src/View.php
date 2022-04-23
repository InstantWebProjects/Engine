<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace InstantWeb\Engine;

use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;

final class View
{
    private const VIEW_EXTENSION = '.html.php';

    public static function render(string $template, string $title = '', array $context = []): string
    {
        $filesystemLoader = new FilesystemLoader(dirname(__DIR__, 3)  . '/views/%name%');

        $templating = new PhpEngine(new TemplateNameParser(), $filesystemLoader);
        $templating->set(new SlotsHelper());

        return $templating->render($template, array_merge($context, ['pageTitle' => $title]));
    }

    public static function output(string $template, string $title, array $context = []): void
    {
        echo self::render($template, $title, $context);
    }

    /**
     * Private constructor to prevent direct object creation.
     */
    private function __construct()
    {
    }

    /**
     * Private cloning to prevent direct object cloning.
     */
    private function __clone()
    {
    }
}
