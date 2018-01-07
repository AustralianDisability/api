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

// Composes POST data for gathering list of urls for download
function composeFileGetRequest($updateId, $device, $info, $rev = 1) {
    $uuid = randStr(8).'-'.randStr(4).'-'.randStr(4).'-'.randStr(4).'-'.randStr(12);

    $createdTime = time();
    $expiresTime = $createdTime + 120;

    $created = gmdate(DATE_W3C, $createdTime);
    $expires = gmdate(DATE_W3C, $expiresTime);

    $flightEnabled = 1;
    $branch = 'rs_prerelease';

    if($info['ring'] == 'RETAIL') {
        $flightEnabled = 0;
        $branch = 'rs2_release';
    }

    return '<s:Envelope xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:s="http://www.w3.org/2003/05/soap-envelope"><s:Header><a:Action s:mustUnderstand="1">http://www.microsoft.com/SoftwareDistribution/Server/ClientWebService/GetExtendedUpdateInfo2</a:Action><a:MessageID>urn:uuid:'.$uuid.'</a:MessageID><a:To s:mustUnderstand="1">https://fe3.delivery.mp.microsoft.com/ClientWebService/client.asmx/secured</a:To><o:Security s:mustUnderstand="1" xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><Timestamp xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd"><Created>'.$created.'</Created><Expires>'.$expires.'</Expires></Timestamp><wuws:WindowsUpdateTicketsToken wsu:id="ClientMSA" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" xmlns:wuws="http://schemas.microsoft.com/msus/2014/10/WindowsUpdateAuthorization"><TicketType Name="MSA" Version="1.0" Policy="MBI_SSL"><Device>'.$device.'</Device></TicketType></wuws:WindowsUpdateTicketsToken></o:Security></s:Header><s:Body><GetExtendedUpdateInfo2 xmlns="http://www.microsoft.com/SoftwareDistribution/Server/ClientWebService"><updateIDs><UpdateIdentity><UpdateID>'.$updateId.'</UpdateID><RevisionNumber>'.$rev.'</RevisionNumber></UpdateIdentity></updateIDs><infoTypes><XmlUpdateFragmentType>FileUrl</XmlUpdateFragmentType><XmlUpdateFragmentType>FileDecryption</XmlUpdateFragmentType></infoTypes><deviceAttributes>E:BranchReadinessLevel=CB&amp;GStatus_RS3=2&amp;PonchAllow=1&amp;CurrentBranch='.$branch.'&amp;FlightContent='.$info['flight'].'&amp;FlightingBranchName=external&amp;FlightRing='.$info['ring'].'&amp;AttrDataVer=29&amp;InstallLanguage=en-US&amp;OSUILocale=en-US&amp;InstallationType=Client&amp;FirmwareVersion=6.00&amp;OSSkuId=48&amp;App=WU&amp;ProcessorManufacturer=GenuineIntel&amp;AppVer='.$info['checkBuild'].'&amp;UpgEx_RS3=Green&amp;OSArchitecture=AMD64&amp;UpdateManagementGroup=2&amp;IsFlightingEnabled='.$flightEnabled.'&amp;IsDeviceRetailDemo=0&amp;TelemetryLevel=1&amp;WuClientVer='.$info['checkBuild'].'&amp;Free=32to64&amp;OSVersion='.$info['checkBuild'].'&amp;DeviceFamily=Windows.Desktop&amp;</deviceAttributes></GetExtendedUpdateInfo2></s:Body></s:Envelope>';
}

// Composes POST data for fetching the latest update information from Windows Update
function composeFetchUpdRequest($device, $encData, $arch, $flight, $ring, $build) {
    $uuid = randStr(8).'-'.randStr(4).'-'.randStr(4).'-'.randStr(4).'-'.randStr(12);

    $createdTime = time();
    $expiresTime = $createdTime + 120;

    $created = gmdate(DATE_W3C, $createdTime);
    $expires = gmdate(DATE_W3C, $expiresTime);

    $flightEnabled = 1;
    $branch = 'rs_prerelease';

    if($ring == 'RETAIL') {
        $flightEnabled = 0;
        $branch = 'rs2_release';
    }

    return '<s:Envelope xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:s="http://www.w3.org/2003/05/soap-envelope"><s:Header><a:Action s:mustUnderstand="1">http://www.microsoft.com/SoftwareDistribution/Server/ClientWebService/SyncUpdates</a:Action><a:MessageID>urn:uuid:'.$uuid.'</a:MessageID><a:To s:mustUnderstand="1">https://fe3.delivery.mp.microsoft.com/ClientWebService/client.asmx</a:To><o:Security s:mustUnderstand="1" xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><Timestamp xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd"><Created>'.$created.'</Created><Expires>'.$expires.'</Expires></Timestamp><wuws:WindowsUpdateTicketsToken wsu:id="ClientMSA" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" xmlns:wuws="http://schemas.microsoft.com/msus/2014/10/WindowsUpdateAuthorization"><TicketType Name="MSA" Version="1.0" Policy="MBI_SSL"><Device>'.$device.'</Device></TicketType></wuws:WindowsUpdateTicketsToken></o:Security></s:Header><s:Body><SyncUpdates xmlns="http://www.microsoft.com/SoftwareDistribution/Server/ClientWebService"><cookie><Expiration>2045-04-07T12:38:34Z</Expiration><EncryptedData>'.$encData.'</EncryptedData></cookie><parameters><ExpressQuery>false</ExpressQuery><InstalledNonLeafUpdateIDs></InstalledNonLeafUpdateIDs><OtherCachedUpdateIDs></OtherCachedUpdateIDs><SkipSoftwareSync>false</SkipSoftwareSync><NeedTwoGroupOutOfScopeUpdates>true</NeedTwoGroupOutOfScopeUpdates><AlsoPerformRegularSync>true</AlsoPerformRegularSync><ComputerSpec/><ExtendedUpdateInfoParameters><XmlUpdateFragmentTypes><XmlUpdateFragmentType>Extended</XmlUpdateFragmentType><XmlUpdateFragmentType>LocalizedProperties</XmlUpdateFragmentType><XmlUpdateFragmentType>Eula</XmlUpdateFragmentType></XmlUpdateFragmentTypes><Locales><string>en-US</string></Locales></ExtendedUpdateInfoParameters><ClientPreferredLanguages></ClientPreferredLanguages><ProductsParameters><SyncCurrentVersionOnly>false</SyncCurrentVersionOnly><DeviceAttributes>E:BranchReadinessLevel=CB&amp;GStatus_RS3=2&amp;PonchAllow=1&amp;CurrentBranch='.$branch.'&amp;FlightContent='.$flight.'&amp;FlightingBranchName=external&amp;FlightRing='.$ring.'&amp;AttrDataVer=29&amp;InstallLanguage=en-US&amp;OSUILocale=en-US&amp;InstallationType=Client&amp;FirmwareVersion=6.00&amp;OSSkuId=48&amp;App=WU&amp;ProcessorManufacturer=GenuineIntel&amp;AppVer='.$build.'&amp;UpgEx_RS3=Green&amp;OSArchitecture=AMD64&amp;UpdateManagementGroup=2&amp;IsFlightingEnabled='.$flightEnabled.'&amp;IsDeviceRetailDemo=0&amp;TelemetryLevel=1&amp;WuClientVer='.$build.'&amp;Free=32to64&amp;OSVersion='.$build.'&amp;DeviceFamily=Windows.Desktop</DeviceAttributes><CallerAttributes>E:Interactive=1&amp;IsSeeker=1&amp;Id=UpdateOrchestrator&amp;</CallerAttributes><Products>PN=Client.OS.rs2.'.$arch.'&amp;Branch='.$branch.'&amp;PrimaryOSProduct=1&amp;V='.$build.';</Products></ProductsParameters></parameters></SyncUpdates></s:Body></s:Envelope>';
}
?>
