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
        $fp = fopen($file, 'rb');

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
            $tokensCount = count($tokens);

            if (false === strpos($buffer, '{')) {
                continue;
            }

            for (; $i < $tokensCount; ++$i) {
                if (T_NAMESPACE === $tokens[$i][0]) {
                    for ($j = $i + 1; $j < $tokensCount; ++$j) {
                        if (T_STRING === $tokens[$j][0] || (\defined('T_NAME_QUALIFIED') && T_NAME_QUALIFIED === $tokens[$j][0])) {
                            $namespace .= '\\'.$tokens[$j][1];
                        } elseif ('{' === $tokens[$j] || ';' === $tokens[$j]) {
                            break;
                        }
                    }
                }

                if (T_CLASS === $tokens[$i][0]) {
                    for ($j = $i + 1; $j < $tokensCount; ++$j) {
                        if ('{' === $tokens[$j]) {
                            $class = $tokens[$i + 2][1];
                        }
                    }
                }
            }
        }

        if (!$class) {
            throw new \RuntimeException('Could not determine class for migration');
        }

        return $namespace.'\\'.$class;
    }
}
