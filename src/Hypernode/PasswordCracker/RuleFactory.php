<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\PasswordCracker;

use Hypernode\PasswordCracker\Mutator;

class RuleFactory
{
    /**
     * @return array
     */
    protected function getValidRules()
    {
        $mutators = array(
            'Hypernode\PasswordCracker\Mutator\AppendCharacter',
            'Hypernode\PasswordCracker\Mutator\Capitalize',
            'Hypernode\PasswordCracker\Mutator\DeleteAtN',
            'Hypernode\PasswordCracker\Mutator\Duplicate',
            'Hypernode\PasswordCracker\Mutator\DuplicateAll',
            'Hypernode\PasswordCracker\Mutator\DuplicateFirstN',
            'Hypernode\PasswordCracker\Mutator\DuplicateLastN',
            'Hypernode\PasswordCracker\Mutator\DuplicateN',
            'Hypernode\PasswordCracker\Mutator\ExtractRange',
            'Hypernode\PasswordCracker\Mutator\InsertAtN',
            'Hypernode\PasswordCracker\Mutator\InvertCapitalize',
            'Hypernode\PasswordCracker\Mutator\Lowercase',
            'Hypernode\PasswordCracker\Mutator\Nothing',
            'Hypernode\PasswordCracker\Mutator\OmitRange',
            'Hypernode\PasswordCracker\Mutator\OverwriteAtN',
            'Hypernode\PasswordCracker\Mutator\PrependCharacter',
            'Hypernode\PasswordCracker\Mutator\Purge',
            'Hypernode\PasswordCracker\Mutator\Reflect',
            'Hypernode\PasswordCracker\Mutator\Replace',
            'Hypernode\PasswordCracker\Mutator\Reverse',
            'Hypernode\PasswordCracker\Mutator\RotateLeft',
            'Hypernode\PasswordCracker\Mutator\RotateRight',
            'Hypernode\PasswordCracker\Mutator\ToggleAt',
            'Hypernode\PasswordCracker\Mutator\ToggleCase',
            'Hypernode\PasswordCracker\Mutator\TruncateRight',
            'Hypernode\PasswordCracker\Mutator\TruncateAtN',
            'Hypernode\PasswordCracker\Mutator\TruncateLeft',
            'Hypernode\PasswordCracker\Mutator\Uppercase',
            'Hypernode\PasswordCracker\Mutator\SwapFront',
            'Hypernode\PasswordCracker\Mutator\SwapBack',
            'Hypernode\PasswordCracker\Mutator\SwapAtN',
            'Hypernode\PasswordCracker\Mutator\BitwiseShiftLeft',
            'Hypernode\PasswordCracker\Mutator\BitwiseShiftRight',
            'Hypernode\PasswordCracker\Mutator\AsciiIncrement',
            'Hypernode\PasswordCracker\Mutator\AsciiDecrement',
            'Hypernode\PasswordCracker\Mutator\ReplaceNPlusOne',
            'Hypernode\PasswordCracker\Mutator\ReplaceNMinusOne',
            'Hypernode\PasswordCracker\Mutator\DuplicateBlockFront',
            'Hypernode\PasswordCracker\Mutator\DuplicateBlockBack',
            'Hypernode\PasswordCracker\Mutator\Title',
            'Hypernode\PasswordCracker\Mutator\Memorize',
            'Hypernode\PasswordCracker\Mutator\PrependMemory',
            'Hypernode\PasswordCracker\Mutator\AppendMemory',
            'Hypernode\PasswordCracker\Mutator\ExtractMemory',
        );

        $m = array();
        foreach ($mutators as $mutator) {
            $id = $mutator::getIdentifier();
            $m[$id] = array(
                'identifier' => $id,
                'length'     => $mutator::getLength(),
                'class'      => $mutator,
            );
        }

        return $m;
    }

    public static function createFromDefinition($definition)
    {

        $validMutators = self::getValidRules();

        $i = 0;
        $mutators = array();
        try {
            while ($i < strlen($definition)) {
                $identifier = $definition[$i];
                if ($identifier === ' ') {
                    $i++;
                    continue;
                }
                if (!isset($validMutators[$identifier])) {
                    throw new \InvalidArgumentException(
                        sprintf('Mutator "%s" not supported in "%s"', $identifier, $definition)
                    );
                }
                $mutator = $validMutators[$identifier];
                $current = '';
                for ($x = 0; $x < $mutator['length']; $x++) {
                    $current .= $definition[$i + $x];
                }

                // validate rule
//                if (!$m[$c]['c']::validate($current)) {
//                    throw new \InvalidArgumentException(sprintf('Invalid mutator "%s" in "%s"', $current, $definition));
//                }

                $mutators[] = new $validMutators[$identifier]['class']($current);

                $i = $i + $mutator['length'];
            }

            return new Rule($mutators);
        } catch (\Exception $e) {
            $failedRules[] = $definition;
        }

        return false;
    }

    /**
     * @param $definitions
     * @return \ArrayIterator
     */
    public static function createFromDefinitionSet($definitions)
    {
        $rules = array();
        foreach ($definitions as $definition) {
            $rule = self::createFromDefinition($definition);
            if ($rule) {
                $rules[] = $rule;
            }
        }

        return new \ArrayIterator($rules);
    }
}
