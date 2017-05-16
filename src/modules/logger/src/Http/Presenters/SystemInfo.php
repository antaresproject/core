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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Http\Presenters;

use Linfo\Linfo;
use Linfo\Common;
use Linfo\Meta\Errors;
use Linfo\Meta\Timer;
use Linfo\Output\Output;

class SystemInfo implements Output
{

    protected $linfo;

    public function __construct(Linfo $linfo)
    {
        $this->linfo = $linfo;
    }

    /**
     * Create a progress bar looking thingy. Put into a function here
     * as its being increasingly used elsewhere. TODO refactor linfo and
     * stop leaving functions in global namespace.
     * @param $percent
     * @param bool $text
     * @return string
     */
    public static function generateBarChart($percent, $text = false)
    {
        return '
			<div class="new_bar_outer">
				<div class="new_bar_bg" style="width: ' . $percent . '%; "></div>
				<div class="new_bar_text">' . ($text ? : $percent . '%') . '</div>
			</div>
		';
    }

    public static function fadedText($text)
    {
        return '<span class="faded">' . $text . '</span>';
    }

    public static function createTable($structure)
    {

        // Start it off
        $html = '
	<div class="infoTable">
		<h2>' . $structure['root_title'] . '</h2>
		<table>';

        // Go throuch each row
        foreach ($structure['rows'] as $row) {

            // Let stuff be killed
            $row['columns'] = array_filter($row['columns']);

            // Ignore this if it's empty
            if (empty($row['columns'])) {
                continue;
            }

            // Start the typical tr
            $html .= '
			<tr>';

            // Is this row a header? 
            if ($row['type'] == 'header') {
                foreach ($row['columns'] as $v) {
                    $html .= is_array($v) ? '
				<th colspan="' . $v[0] . '"' . (array_key_exists('2', $v) ? ' style="width: ' . $v[2] . ';"' : '') . '>' . $v[1] . '</th>' : '
				<th>' . $v . '</th>';
                }
            }

            // Or is it a row saying nothing was found?
            elseif ($row['type'] == 'none') {
                foreach ($row['columns'] as $v) {
                    $html .= is_array($v) ? '
				<td colspan="' . $v[0] . '" class="none">' . $v[1] . '</td>' : '
				<td class="none">' . $v . '</td>';
                }
            }

            // Or is it values?
            elseif ($row['type'] == 'values') {
                foreach ($row['columns'] as $v) {
                    $html .= is_array($v) ? '
				<td colspan="' . $v[0] . '">' . $v[1] . '</td>' : '
				<td>' . $v . '</td>';
                }
            }

            // End the usual tr
            $html .= '
			</tr>';
        }

        // Closing tags
        $html .= '
		</table>
	</div>';

        // Give it
        return $html;
    }

    public function output()
    {
        $lang     = $this->linfo->getLang();
        $settings = $this->linfo->getSettings();
        $info     = $this->linfo->getInfo();
        $appName  = $this->linfo->getAppName();
        $version  = $this->linfo->getVersion();

        // Fun icons
        $show_icons  = array_key_exists('icons', $settings) ? !empty($settings['icons']) : true;
        $os_icon     = $info['OS'] == 'Windows' ? 'windows' : strtolower(str_replace(' ', '', current(explode('(', $info['OS']))));
        $distro_icon = $info['OS'] == 'Linux' && is_array($info['Distro']) && $info['Distro']['name'] ? strtolower(str_replace(' ', '', $info['Distro']['name'])) : false;

        // Start compressed output buffering. Try to not do this if we've had errors or otherwise already outputted stuff
        if ((!function_exists('error_get_last') || !error_get_last()) && (!isset($settings['compress_content']) || $settings['compress_content'])) {
            ob_start(function_exists('ob_gzhandler') ? 'ob_gzhandler' : null);
        }

        // See if we have a specific theme file installed
        if (isset($settings['theme']) && strpos($settings['theme'], '..') === false && file_exists('layout/theme_' . $settings['theme'] . '.css')) {
            $theme_css = 'theme_' . $settings['theme'] . '.css';
        }

        // Does default exist?? Don't bitch at me for assigning an array key in an if-then
        elseif (($settings['theme'] = 'default') && file_exists('layout/theme_' . $settings['theme'] . '.css')) {
            $theme_css = 'theme_' . $settings['theme'] . '.css';
        }

        // if not, do the old way
        else {
            $theme_css = 'styles.css';
        }

        $core = array();

        // OS? (with icon, if we have it)
        if (!empty($settings['show']['os'])) {
            $core[] = array($lang['os'], ($show_icons && (file_exists($this->linfo->getLocalDir() . 'layout/icons/os_' . $os_icon . '.gif') || file_exists($this->linfo->getLocalDir() . 'layout/icons/os_' . $os_icon . '.png')) ? '<span class="icon icon_os_' . $os_icon . '"></span>' : '') . $info['OS']);
        }

        // Distribution? (with icon, if we have it)
        if (!empty($settings['show']['distro']) && array_key_exists('Distro', $info) && is_array($info['Distro'])) {
            $core[] = array($lang['distro'], ($show_icons && $distro_icon && (file_exists($this->linfo->getLocalDir() . 'layout/icons/distro_' . $distro_icon . '.gif') || file_exists($this->linfo->getLocalDir() . 'layout/icons/distro_' . $distro_icon . '.png')) ? '<span class="icon icon_distro_' . $distro_icon . '"></span>' : '') . $info['Distro']['name'] . ($info['Distro']['version'] ? ' - ' . $info['Distro']['version'] : ''));
        }

        // Virtualization
        if (!empty($settings['show']['virtualization']) && isset($info['virtualization']) && !empty($info['virtualization'])) {
            $vmval = false;

            if ($info['virtualization']['type'] == 'guest') {
                $vmval = '<span class="icon icon_vm_' . str_replace('/', '_', strtolower($info['virtualization']['method'])) . '"></span>' . $info['virtualization']['method'] . ' ' . $lang['guest'];
            } elseif ($info['virtualization']['type'] == 'host') {
                $vmval = '<span class="icon icon_vm_' . str_replace('/', '_', strtolower($info['virtualization']['method'])) . '"></span>' . $info['virtualization']['method'] . ' ' . $lang['host'];
            }

            if ($vmval) {
                $core[] = array($lang['virtualization'], $vmval);
            }
        }

        // Kernel
        if (!empty($settings['show']['kernel'])) {
            $core[] = array($lang['kernel'], $info['Kernel']);
        }

        // Model?
        if (!empty($settings['show']['model']) && array_key_exists('Model', $info) && !empty($info['Model'])) {
            $core[] = array($lang['model'], $info['Model']);
        }

        // IP
        if (!isset($settings['show']['ip']) || !empty($settings['show']['ip'])) {
            $core[] = array($lang['accessed_ip'], $info['AccessedIP']);
        }

        // Uptime
        if (!empty($settings['show']['uptime']) && $info['UpTime']) {
            $core[] = array($lang['uptime'],
                $info['UpTime']['text'] .
                (isset($info['UpTime']['bootedTimestamp']) && $info['UpTime']['bootedTimestamp'] ? '; booted ' . date($settings['dates'], $info['UpTime']['bootedTimestamp']) : ''),);
        }

        // Hostname
        if (!empty($settings['show']['hostname'])) {
            $core[] = array($lang['hostname'], $info['HostName']);
        }

        //Web server
        if (!empty($settings['show']['webservice'])) {
            $core[] = array($lang['webservice'], $info['webService']);
        }

        //Php version
        if (!empty($settings['show']['phpversion'])) {
            $core[] = array($lang['phpversion'], $info['phpVersion']);
        }

        // The CPUs
        if (!empty($settings['show']['cpu'])) {
            $cpus = array();

            foreach ((array) $info['CPU'] as $cpu) {
                $cpu_html = (array_key_exists('Vendor', $cpu) && !empty($cpu['Vendor']) ? $cpu['Vendor'] . ' - ' : '') .
                        $cpu['Model'] .
                        (array_key_exists('MHz', $cpu) ?
                                ($cpu['MHz'] < 1000 ? ' (' . $cpu['MHz'] . ' MHz)' : ' (' . round($cpu['MHz'] / 1000, 3) . ' GHz)') : '') .
                        (array_key_exists('usage_percentage', $cpu) ? ' (' . $cpu['usage_percentage'] . '%)' : '');

                if (array_key_exists('usage_percentage', $cpu)) {
                    $cpu_html = '<div class="new_bar_left" style="margin-top: 3px; margin-bottom: 3px;">' . self::generateBarChart($cpu['usage_percentage'], $cpu_html) . '</div>';
                } else {
                    $cpu_html .= '<br>';
                }

                $cpus[] = $cpu_html;
            }
            $core[] = array('CPUs (' . count($info['CPU']) . ')', implode('', $cpus));
        }

        // CPU Usage?
        if (!empty($settings['cpu_usage']) && isset($info['cpuUsage']) && $info['cpuUsage'] !== false) {
            $core[] = array($lang['cpu_usage'], self::generateBarChart($info['cpuUsage']));
        }

        // System Load
        if (!empty($settings['show']['load'])) {
            $core[] = array($lang['load'], implode(' ', (array) $info['Load']));
        }

        // CPU architecture. Permissions goes hand in hand with normal CPU
        if (!empty($settings['show']['cpu']) && array_key_exists('CPUArchitecture', $info)) {
            $core[] = array($lang['cpu_arch'], $info['CPUArchitecture']);
        }

        // We very well may not have process stats
        if (!empty($settings['show']['process_stats']) && $info['processStats']['exists']) {

            // Different os' have different keys of info
            $proc_stats = array();

            // Load the keys
            if (array_key_exists('totals', $info['processStats']) && is_array($info['processStats']['totals'])) {
                foreach ($info['processStats']['totals'] as $k => $v) {
                    $proc_stats[] = $k . ': ' . number_format($v);
                }
            }

            // Total as well
            $proc_stats[] = 'total: ' . number_format($info['processStats']['proc_total']);

            // Show them
            $core[] = array($lang['processes'], implode('; ', $proc_stats));

            // We might not have threads
            if ($info['processStats']['threads'] !== false) {
                $core[] = array($lang['threads'], number_format($info['processStats']['threads']));
            }
        }

        // Users with active shells
        if (!empty($settings['show']['numLoggedIn']) && array_key_exists('numLoggedIn', $info) && $info['numLoggedIn']) {
            $core[] = array($lang['numLoggedIn'], $info['numLoggedIn']);
        }

        // Show memory?
        $byteConvert = function($input) {
            return Common::byteConvert($input);
        };
        $generateBarChart = function($input) {
            return self::generateBarChart($input);
        };
        $fadedText = function($input) {
            return self::fadedText($input);
        };

        $args = [
            'info'             => $info,
            'lang'             => $lang,
            'core'             => $core,
            'byteConvert'      => $byteConvert,
            'generateBarChart' => $generateBarChart,
            'settings'         => $settings,
            'fadedText'        => $fadedText
        ];

        if (!empty($settings['show']['ram'])) {

            $have_swap = (isset($info['RAM']['swapFree']) || isset($info['RAM']['swapTotal']));

            $args['have_swap'] = $have_swap;

            if ($have_swap && !empty($info['RAM']['swapTotal'])) {
                // Show detailed swap info?
                $show_detailed_swap         = is_array($info['RAM']['swapInfo']) && count($info['RAM']['swapInfo']) > 0;
                $args['show_detailed_swap'] = $show_detailed_swap;
            }
        }



        // Network Devices?
        if (!empty($settings['show']['network'])) {

            $show_type          = array_key_exists('nic_type', $info['contains']) ? $info['contains']['nic_type'] : true;
            $show_speed         = array_key_exists('nic_port_speed', $info['contains']) ? $info['contains']['nic_port_speed'] : true;
            $args['show_type']  = $show_type;
            $args['show_speed'] = $show_speed;
        }

        if (!empty($settings['show']['devices'])) {
            $show_vendor         = array_key_exists('hw_vendor', $info['contains']) ? ($info['contains']['hw_vendor'] === false ? false : true) : true;
            $args['show_vendor'] = $show_vendor;
        }


        // Show file system mounts?
        if (!empty($settings['show']['mounts'])) {
            $has_devices = false;
            $has_labels  = false;
            $has_types   = false;
            foreach ($info['Mounts'] as $mount) {
                if (!empty($mount['device'])) {
                    $has_devices = true;
                }
                if (!empty($mount['label'])) {
                    $has_labels = true;
                }
                if (!empty($mount['devtype'])) {
                    $has_types = true;
                }
            }
            $addcolumns = 0;
            if ($settings['show']['mounts_options']) {
                $addcolumns++;
            }
            if ($has_devices) {
                $addcolumns++;
            }
            if ($has_labels) {
                $addcolumns++;
            }
            if ($has_types) {
                $addcolumns++;
            }

            $args['has_types']   = $has_types;
            $args['has_devices'] = $has_devices;
            $args['has_labels']  = $has_labels;

            // Calc totals
            $total_size = 0;
            $total_used = 0;
            $total_free = 0;

            // Don't add totals for duplicates. (same filesystem mount twice in different places)
            $done_devices       = array();
            $args['addcolumns'] = $addcolumns;
            // Are there any?
            if (count($info['Mounts']) > 0) {
                $mounts = [];
                // Go through each
                foreach ($info['Mounts'] as $mount) {

                    // Only add totals for this device if we haven't already
                    if (!in_array($mount['device'], $done_devices)) {
                        $total_size += $mount['size'];
                        $total_used += $mount['used'];
                        $total_free += $mount['free'];
                        if (!empty($mount['device'])) {
                            $done_devices[] = $mount['device'];
                        }
                    }

                    // Possibly don't show this twice
                    elseif (array_key_exists('duplicate_mounts', $settings['show']) && empty($settings['show']['duplicate_mounts'])) {
                        continue;
                    }

                    // If it's an NFS mount it's likely in the form of server:path (without a trailing slash), 
                    // but if the path is just / it likely just shows up as server:,
                    // which is vague. If there isn't a /, add one
                    if (preg_match('/^.+:$/', $mount['device']) == 1) {
                        $mount['device'] .= DIRECTORY_SEPARATOR;
                    }

                    $mounts[] = $mount;
                }
                $args['mounts'] = $mounts;
            }

            // Show totals and finish table
            $total_used_perc         = $total_size > 0 && $total_used > 0 ? round($total_used / $total_size, 2) * 100 : 0;
            $args['total_used_perc'] = $total_used_perc;
            $args['total_size']      = $total_size;
            $args['total_used']      = $total_used;
            $args['total_free']      = $total_free;
        }


        $errorsNum          = Errors::num();
        $errorsShow         = Errors::show();
        $args['errorsNum']  = $errorsNum;
        $args['errorsShow'] = $errorsShow;


        $createTable = function($input) {
            return self::createTable($input);
        };
        $args['createTable']  = $createTable;
        // Additional extensions
        $args['timerResults'] = Timer::getResults();
        publish('logger', 'scripts.resources');
        return view('antares/logger::admin.system.system', $args)->render();
    }

}
