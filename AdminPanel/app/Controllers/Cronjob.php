<?php

namespace App\Controllers;

use \App\Libraries\UserShell;
use \App\Models\Settings;
use \App\Models\Users;
use \App\Models\Traffics;

class Cronjob extends BaseController
{

    public function master($request, $response, $arg)
    {
        $this->multiUser($request, $response, $arg);
        $this->expireUsers($request, $response, $arg);
        $this->syncTraffic($request, $response, $arg);
        $this->getAppLastVersion($request, $response, $arg);
    }

    public function getAppLastVersion()
    {
        $lastVersion = githubLastVersion();
        if ($lastVersion) {
            $where  = ["name" => "app_last_version"];
            $values = ["name" => "app_last_version", "value" => $lastVersion];
            \App\Models\Settings::updateOrCreate($where, $values);
        }
    }

    public function multiUser($request, $response, $args)
    {

        $multiuser      = Settings::getSetting("multiuser");
        $onlineUsers    = UserShell::onlineUsers();

        $uModel         = new Users();

        if (!empty($onlineUsers)) {
            foreach ($onlineUsers as $username => $users) {

                $userInfo =  $uModel->getByUsername($username);

                if ($userInfo) {;
                    $expiryDays   = $userInfo->expiry_days;
                    $limitUsers   = $userInfo->limit_users;

                    //set expiry date
                    if (!$userInfo->start_date) {
                        $startDate  = date("Y/m/d");
                        $endDate    = date('Y/m/d', strtotime($startDate . " + $expiryDays days"));
                        $uModel->updateExpirydates($username, $startDate, $endDate);
                    }

                    if ($multiuser && count($users) > $limitUsers) {
                        UserShell::disableMultiUser($username);
                    }
                }
            }
        }
    }

    public function expireUsers($request, $response, $args)
    {
        $this->syncServUsersWithDB();
        $this->expiredUsers();
    }

    public function syncTraffic($request, $response, $args)
    {

        $trafficFilePath = PATH_STORAGE . DS . "traffics.json";
        $tModel          = new Traffics();

        if (file_exists($trafficFilePath)) {
            $fileContent    = file_get_contents($trafficFilePath);
            $fileLines      = explode("\n", $fileContent);

            $trafficItems   = [];
            foreach ($fileLines as $fileLine) {
                // Trim any extra whitespace
                $trimmedLine = trim($fileLine);
                if (!empty($trimmedLine)) {
                    $jsonData = json_decode($trimmedLine, true);
                    if ($jsonData !== null) {
                        $trafficItems = array_merge($trafficItems, $jsonData);
                    }
                }
            }



            //all server users 
            $serverUsers    = UserShell::allUsers();
            $userTraffics   = [];


            foreach ($trafficItems as $tItem) {
                $username = !empty($tItem["name"]) ? $tItem["name"] : "";
                $username = str_replace("sshd: ", "", $username);
                if (in_array($username, $serverUsers)) {
                    $rx = !empty($tItem["RX"]) ? $tItem["RX"] : 0;
                    $tx = !empty($tItem["TX"]) ? $tItem["TX"] : 0;

                    $rx = round($rx);
                    $rx = ($rx) / 10;
                    $rx = round(($rx / 12) * 100);
                    $tx = round($tx);
                    $tx = ($tx) / 10;
                    $tx = round(($tx / 12) * 100);
                    $total  = $rx + $tx;

                    $userTraffics[$username] = [
                        "rx"    => $rx,
                        "tx"    => $tx,
                        "total" => $total
                    ];
                }
            }

            //update database
            foreach ($userTraffics as $username => $traffic) {
                $userTraffic = $tModel->getUserTraffic($username);

                $trafficColumn = [
                    "upload"    => $traffic["tx"],
                    "download"  => $traffic["rx"],
                    "total"     => $traffic["total"],
                ];
                if ($userTraffic) {
                    $trafficColumn["upload"]    += $userTraffic->upload;
                    $trafficColumn["download"]  += $userTraffic->download;
                    $trafficColumn["total"]     += $userTraffic->total;
                    $trafficColumn["utime"]     = time();
                } else {
                    $trafficColumn["ctime"]     = time();
                    $trafficColumn["utime"]     = 0;
                }

                Traffics::updateOrCreate(["username" => $username], $trafficColumn);
            }
        }


        UserShell::createTrfficsLogFile($trafficFilePath);
    }

    /** private methods */
    private function syncServUsersWithDB()
    {
        $usersList  = UserShell::allUsers();
        $uModel     = new Users();

        foreach ($usersList as $username) {
            if ($username !== "videocall") {
                $userInfo =  $uModel->getByUsername($username);
                if (!$userInfo) {
                    UserShell::deleteUser($username);
                }
            }
        }
    }

    private function expiredUsers()
    {
        $uModel         = new Users();
        $activeUsers    = $uModel->activeUsers();

        if ($activeUsers) {
            foreach ($activeUsers as $user) {
                $userId         = $user->id;
                $username       = $user->username;
                $totalTraffic   = $user->traffic;
                $cTraffic       = $user->consumer_traffic;
                $cTraffic       = $cTraffic ? $cTraffic : 0;

                $endDate        = $user->end_date;
                $startDate      = $user->start_date;

                if ($startDate) {
                    $isReset = false;
                    if (time() > $endDate) {
                        $isReset = true;
                    }
                    if ($totalTraffic && ($cTraffic > $totalTraffic)) {
                        $isReset = true;
                    }

                    if ($isReset) {
                        UserShell::deactivateUser($username);
                        if ($cTraffic >= $totalTraffic) {
                            $uModel->updateStatus($userId, "expiry_traffic");
                        } else {
                            $uModel->updateStatus($userId, "expiry_date");
                        }
                    }
                }
            }
        }
    }
}
