<?php

/*
 * This file is part of the PHPCR Migrations package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Migrations;

class MigratorUtil
{
    /**
     * Return the class name from a file.
     *
     * Taken from http://stackoverflow.com/questions/7153000/get-class-name-from-file
     *
     * @param string $file
     *
     * @return string
     */
    public static function getClassNameFromFile($file)
    {
        $fp = fopen($file, 'r');

        $class = $namespace = $buffer = '';
        $i = 0;

        while (!$class) {
            if (feof($fp)) {
                break;
            }

            // Read entire lines to prevent keyword truncation
            for ($line = 0; $line <= 20; ++$line) {
                $buffer .= fgets($fp);
            }
            $tokens = @token_get_all($buffer);

            if (false === strpos($buffer, '{')) {
                continue;
            }

            for (; $i < count($tokens); ++$i) {
                if (T_NAMESPACE === $tokens[$i][0]) {
                    for ($j = $i + 1; $j < count($tokens); ++$j) {
                        if (\defined('T_NAME_QUALIFIED') && T_NAME_QUALIFIED === $tokens[$j][0] || T_STRING === $tokens[$j][0]) {
                            $namespace .= '\\'.$tokens[$j][1];
                        } elseif ('{' === $tokens[$j] || ';' === $tokens[$j]) {
                            break;
                        }
                    }
                }

                if (T_CLASS === $tokens[$i][0]) {
                    for ($j = $i + 1; $j < count($tokens); ++$j) {
                        if ('{' === $tokens[$j]) {
                            $class = $tokens[$i + 2][1];
                        }
                    }
                }
            }
        }

        if (!$class) {
            return;
        }

        return $namespace.'\\'.$class;
    }
}
