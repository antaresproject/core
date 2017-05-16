<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications\Adapter;

use Illuminate\Database\Eloquent\Collection;
use Antares\View\Notification\Notification;
use Illuminate\Database\Eloquent\Builder;
use Twig_Loader_String;
use TwigBridge\Bridge;
use Twig_Environment;
use Exception;

class VariablesAdapter
{

    protected $variables = [];

    public function setVariables($variables = [])
    {
        $this->variables = $variables;
        return $this;
    }

    /**
     * Gets variables filled notification content
     * 
     * @param String $content
     * @param array $variables
     * @return String
     */
    public function get($content, array $variables = [])
    {

        $replacements = [];
        $instruction  = 'foreach';


        foreach ($variables as $key => $values) {
            $inner = $this->getInnerInstruction($content, $instruction);
            foreach ($inner as $config) {
                if (!str_contains($config['cleared'], $key)) {
                    continue;
                }
                $replacements[$key] = $inner;
            }
        }
        if (!empty($replacements)) {
            foreach ($replacements as $key => $value) {
                foreach ($value as $element) {
                    $inner   = $this->renderByTwig($element['cleared'], $variables);
                    $content = str_replace(["[[{$instruction}]]", "[[/{$instruction}]]", $element['to_replace']], ['', '', $inner], $content);
                }
            }
        }

        $content = str_replace(['<p>', '</p>', '<br />'], '', $content);
        $filled  = $this->fill($content);

        preg_match_all('/\[\[(.*?)\]\]/', $content, $matches);

        if (!isset($matches[0]) or ! isset($matches[1])) {
            return $filled;
        }
        foreach ($matches[0] as $index => $variable) {
            $filled = str_replace($variable, '{{ ' . $matches[1][$index] . ' }}', $filled);
        }
        return $this->renderByTwig($filled, array_merge($variables, $this->variables));
    }

    protected function renderByTwig($view, $params)
    {
        $twig        = app(Bridge::class);
        $loaderChain = $twig->getLoader();
        $twig->setLoader(new Twig_Loader_String());
        $rendered    = $twig->render($view, $params);
        $twig->setLoader($loaderChain);
        return $rendered;
    }

    /**
     * Innser instruction
     * 
     * @param Stirng $content
     * @param String $instruction
     * @return String
     */
    protected function getInnerInstruction($content, $instruction)
    {
        $sources      = $this->getStringsBetween($content, "[[{$instruction}]]", "[[/{$instruction}]]");
        $replacements = [];
        foreach ($sources as $index => $source) {
            $toReplace            = $source;
            $source               = $this->clearCondition($source);
            $replacements[$index] = [
                'to_replace' => $toReplace,
                'cleared'    => $source
            ];
        }
        return $replacements;
    }

    /**
     * fill notification notification content with variables
     * 
     * @param String $content
     * @return String
     */
    public function fill($content)
    {
        if ($this->hasInstructions($content)) {
            $instructions = $this->getInstructions();
            foreach ($instructions as $instruction) {
                $content = $this->parseInstructions($content, $instruction);
            }
        }
        return $this->fillContent($content);
    }

    /**
     * get list of notification instructions
     * 
     * @return array
     */
    protected function getInstructions()
    {
        $instructions = Notification::getInstructions();
        if (empty($instructions)) {
            return [];
        }
        return array_keys($instructions);
    }

    /**
     * whether notification has instructions
     * 
     * @param String $content
     * @return boolean
     */
    protected function hasInstructions($content)
    {
        $instructionKeys = $this->getInstructions();
        foreach ($instructionKeys as $instructionKey) {
            if (str_contains($content, ['[[' . $instructionKey . ']]', '[[/' . $instructionKey . ']]'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * parsing instructions in notification content
     * 
     * @param String $content
     * @param String $instruction
     * @return String
     */
    protected function parseInstructions($content, $instruction)
    {
        $twig    = new Twig_Environment(new Twig_Loader_String());
        $sources = $this->getStringsBetween($content, "[[{$instruction}]]", "[[/{$instruction}]]");
        foreach ($sources as $source) {
            $toReplace = $source;
            $source    = $this->clearCondition($source);
            if ($source == '') {
                return $content;
            }
            $variables    = $this->extractVariables($source);
            $renderParams = [];

            foreach (array_keys($variables) as $index => $key) {
                $localVariable                = 'var_' . $index;
                $source                       = str_replace($key, $localVariable, $source);
                $renderParams[$localVariable] = $variables[$key];
            }
            $content = str_replace(["[[{$instruction}]]", "[[/{$instruction}]]", $toReplace], ['', '', $twig->render($source, $renderParams)], $content);
        }
        return $content;
    }

    /**
     * clear conditions in notification
     * 
     * @param String $source
     * @return String
     */
    protected function clearCondition($source)
    {
        $line = $this->getStringBetween($source, '{%', '%}');
        if (strlen($line) > 0) {
            return str_replace(['&#39;', '&nbsp;'], ["'", ' '], $source);
        }
        return $source;
    }

    /**
     * gets list of occurences between two strings
     * 
     * @param String $string
     * @param String $start
     * @param String $end
     * @return array
     */
    function getStringsBetween($string, $start, $end)
    {
        $lastPos   = 0;
        $positions = array();

        while (($lastPos = strpos($string, $start, $lastPos)) !== false) {
            $positions[] = $lastPos;
            $lastPos     = $lastPos + strlen($start);
        }
        $lastPos      = 0;
        $positionsEnd = array();
        while (($lastPos      = strpos($string, $end, $lastPos)) !== false) {
            $positionsEnd[] = $lastPos;
            $lastPos        = $lastPos + strlen($end);
        }
        $return = [];
        foreach ($positions as $index => $position) {
            $return[] = str_replace([$start, $end], '', substr($string, $position, $positionsEnd[$index] - $position));
        }
        return $return;
    }

    /**
     * gets string between two strings
     * 
     * @param String $string
     * @param String $start
     * @param String $end
     * @return String
     */
    function getStringBetween($string, $start, $end)
    {
        $string = " " . $string;
        $ini    = strpos($string, $start);

        if ($ini == 0) {
            return "";
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * extract variables from notification
     * 
     * @param String $content
     * @return boolean
     */
    protected function extractVariables($content)
    {
        preg_match_all('/\[\[(.*?)\]\]/', $content, $matches);
        if (!isset($matches[0]) or ! isset($matches[1])) {
            return false;
        }
        $return        = [];
        $notifications = app('antares.notifications')->all();
        foreach ($matches[1] as $index => $match) {
            if (!str_contains($match, '::')) {
                continue;
            }
            list($component, $var) = explode('::', trim($match));
            $name = $this->findExtensionName($component);
            if (!$name) {
                continue;
            }
            $variables                   = $notifications[$name]['variables'];
            $return[$matches[0][$index]] = $this->resolveValue($var, $variables);
        }
        return $return;
    }

    /**
     * fills content with notification variables values
     * 
     * @param String $content
     * @return String
     */
    protected function fillContent($content)
    {
        preg_match_all('/\[\[(.*?)\]\]/', $content, $matches);
        if (!isset($matches[0]) or ! isset($matches[1])) {
            return $content;
        }
        $notifications = app('antares.notifications')->all();
        foreach ($matches[1] as $index => $match) {
            if (!str_contains($match, '::')) {
                continue;
            }
            list($component, $var) = explode('::', trim($match));
            $name = $this->findExtensionName($component);

            $variables = isset($notifications[$name]['variables']) ? $notifications[$name]['variables'] : [];
            event('notifications:notification.variables', [&$variables]);
            $value     = $this->resolveValue($var, $variables);
            if (is_array($value)) {
                $value = $this->getDefaultNotificationForList($value)->render();
            }
            $content = str_replace($matches[0][$index], $value, $content);
        }
        return $content;
    }

    /**
     * resolve notification variable value
     * 
     * @param String $name
     * @param array $variables
     * @return mixed
     */
    protected function resolveValue($name, $variables)
    {
        if (!empty($variables)) {
            foreach ($variables as $container => $vars) {
                foreach ($vars as $subname => $var) {
                    if (isset($var['name']) and $name == $var['name'] and isset($var['value'])) {
                        return $var['value'];
                    } elseif (!isset($var['name']) and $subname == $name and isset($var['value'])) {
                        return $var['value'];
                    }
                }
            }
        }
        if (!isset($variables[$name])) {
            return false;
        }
        $variable = $variables[$name];
        if (isset($variable['dataProvider'])) {
            $value = $this->resolveDataProviderValue($variable['dataProvider']);
            if (!$value) {
                return false;
            }
            return $value;
        }
        return isset($variable['value']) ? $variable['value'] : '';
    }

    /**
     * resolve notification variable value from data provider
     * 
     * @param mixed $dataProvider
     * @return boolean|Collection
     */
    protected function resolveDataProviderValue($dataProvider)
    {
        preg_match("/(.*)@(.*)/", $dataProvider, $matches);
        if (!isset($matches[1]) and ! isset($matches[2])) {
            return false;
        }
        if (!class_exists($matches[1])) {
            return false;
        }
        try {
            $instance = app($matches[1]);
            $return   = call_user_func_array([$instance, $matches[2]], []);
            if ($return instanceof Builder) {
                return $return->get()->toArray();
            }
            if ($return instanceof Collection) {
                return $return->toArray();
            }
            return $return;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * get extension key name by name from manifest
     * 
     * @param type $name
     * @return boolean|String
     */
    protected function findExtensionName($name)
    {
        if ($name == 'foundation') {
            return $name;
        }
        $extensions = app('antares.memory')->make('component')->get('extensions.active');
        foreach ($extensions as $keyname => $extension) {
            if ($name == $extension['name']) {
                return $keyname;
            }
        }
        return false;
    }

    /**
     * gets default notification when notification variable is array
     * 
     * @param array $list
     * @return View
     */
    public function getDefaultNotificationForList($list)
    {
        return view('antares/notifications::template.list', compact('list'));
    }

}
