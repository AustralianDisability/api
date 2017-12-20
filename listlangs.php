<?php
/*
Copyright 2017 UUP dump API authors

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

require_once dirname(__FILE__).'/shared/main.php';
require_once dirname(__FILE__).'/shared/packs.php';

function uupListLangs($updateId = 0) {
    if($updateId) {
        $info = uupUpdateInfo($updateId, 'build');
    }

    if(isset($info['info'])) {
        $build = explode('.', $info['info']);
        $build = $build[0];
    } else {
        $build = 9841;
    }

    $packs = uupGetPacks($build);
    $packsForLangs = $packs['packsForLangs'];
    $fancyLangNames = $packs['fancyLangNames'];

    $langList = array();
    $langListFancy = array();
    foreach($packsForLangs as $key => $val) {
        if(isset($packsForLangs[$key])) {
            $fancyName = $fancyLangNames[$key];
        } else {
            $fancyName = $key;
        }

        $temp = array($key => $fancyName);
        $langList = array_merge($langList, array($key));
        $langListFancy = array_merge($langListFancy, $temp);
    }

    return array(
        'apiVersion' => uupApiVersion(),
        'langList' => $langList,
        'langFancyNames' => $langListFancy,
    );
}
?>
