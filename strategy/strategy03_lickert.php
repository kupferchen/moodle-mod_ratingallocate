<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Internal library of functions for module ratingallocate
 *
 * All the ratingallocate specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package mod_ratingallocate
 * @copyright 2014 M Schulze
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// namespace is mandatory!

namespace ratingallocate\strategy_lickert;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/../locallib.php');
require_once(dirname(__FILE__) . '/strategy_template_options.php');

class strategy extends \strategytemplate_options {

    const STRATEGYID = 'strategy_lickert';
    const MAXNO = 'maxno';
    const COUNTLICKERT = 'countlickert';

    public function get_strategyid() {
        return self::STRATEGYID;
    }

    public function get_static_settingfields() {
        return array(
            self::MAXNO => array(// maximale Anzahl 'kannnicht'
                'int',
                get_string(self::STRATEGYID . '_setting_maxno', ratingallocate_MOD_NAME)
            ),
            self::COUNTLICKERT => array(// wie viele Felder es gibt
                'int',
                get_string(self::STRATEGYID . '_setting_maxlickert', ratingallocate_MOD_NAME)
            )
        );
    }

    
    public function get_dynamic_settingfields(){
        $maxlickert = $this->get_settings_value(self::COUNTLICKERT);
        $output = array();
        foreach($this->get_choiceoptions($maxlickert) as $id => $option){
            $output[$id] = array(
                'text',
                $option
            );
        }
        return $output;
    }

    public function get_choiceoptions($consider_dafault = false, $consider_custom = true, $maxlickert = 0) {
        $options = array();
        for ($i = 0; $i <= $maxlickert; $i++) {
            $options[$i] = $this->get_settings_value($i, $consider_dafault, $consider_custom);
        }
        return $options;
    }


    public function get_default_settings($maxlickert = 0){
        $defaults = array(
                        self::MAXNO => 3,
                        self::COUNTLICKERT => 4,
                        0 => '0 - '.get_string(strategy::STRATEGYID . '_rating_exclude', ratingallocate_MOD_NAME)
        );
        
        for ($i = 1; $i <= $maxlickert; $i++) {
            if ($i == $maxlickert) {
                $defaults[$i] = $i.' - '.get_string(strategy::STRATEGYID . '_rating_biggestwish', ratingallocate_MOD_NAME);
            } else {
                $defaults[$i] = $i;
            }
        }
        return $defaults;
    }
}

// register with the strategymanager
\strategymanager::add_strategy(strategy::STRATEGYID);

class mod_ratingallocate_view_form extends \ratingallocate_options_strategyform {
    //Already specified by parent class

    public function get_choiceoptions($params = null) {
        $params = $this->get_strategysetting(strategy::COUNTLICKERT);
        return strategy::get_choiceoptions($params);
    }

    protected function get_max_amount_of_nos() {
        return $this->get_strategysetting(strategy::MAXNO);
    }

    protected function get_max_nos_string_identyfier() {
        return strategy::STRATEGYID . '_max_no';
    }
}