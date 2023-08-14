<?php

/**
 * By MahmoudAp
 * Github: https://github.com/mahmoud-ap
 */

namespace App\Models;

use Morilog\Jalali\Jalalian;
use \App\Libraries\UserShell;

class Users extends \App\Models\BaseModel
{

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'username',
        'admin_uname',
        'password',
        'email',
        'mobile',
        'desc',
        'limit_users',
        'start_date',
        'end_date',
        'expiry_days',
        'expiry_type',
        'status',
        'traffic',
        'ctime',
        'utime',
    ];

    public function saveUsers($pdata, $uid, $editId = null)
    {
        $columnsArr    = [];
        $pdata          = trimArrayValues($pdata);

        $userInfo       = $editId ? $this->getInfo($editId) : null;
        $userStartDate  = $userInfo ? $userInfo->start_date : "";


        $startDate      = 0;
        $endDate        = 0;
        $expiryType     = getArrayValue($pdata, "expiry_type", $userStartDate ? "date" : "days");

        $columnsArr["expiry_days"] = getArrayValue($pdata, "exp_days", 0);

        if ($expiryType == "date") {
            $expDate    = $pdata["exp_date"];
            $expDateST  = strtotime(Jalalian::fromFormat('Y/m/d', $expDate)->toCarbon());
            $startDate  = time();
            $endDate    = strtotime("tomorrow", $expDateST) - 1;
            $columnsArr["expiry_days"]  = floor(($endDate - $startDate) / 86400);
        }

        $columnsArr["password"]         = $pdata["password"];
        $columnsArr["email"]            = getArrayValue($pdata, "email");
        $columnsArr["mobile"]           = getArrayValue($pdata, "mobile");
        $columnsArr["desc"]             = getArrayValue($pdata, "desc");
        $columnsArr["start_date"]       = $startDate;
        $columnsArr["end_date"]         = $endDate;
        $columnsArr["traffic"]          = $pdata["traffic"] * 1024;
        $columnsArr["limit_users"]      = $pdata["limit_users"];

        if (!$editId) {
            $columnsArr["username"]         = $pdata["username"];
            $columnsArr["admin_uname"]      = getAdminUsername();
            $columnsArr["ctime"]            = time();
            $columnsArr["utime"]            = 0;
            $columnsArr["status"]           = "active";
        } else {
            $columnsArr["utime"]            = time();
            if ($userInfo->is_expired && $endDate > time()) {
                $columnsArr["status"]        = "active";
                $columnsArr["start_date"]    = time();
            }
        }

        try {
            db()::transaction(function () use ($columnsArr, $editId, $userInfo) {

                $this->updateOrCreate(['id' => $editId], $columnsArr);
                if (!$editId) {
                    $trafficCols["username"]    = $columnsArr["username"];
                    $trafficCols["download"]    = 0;
                    $trafficCols["upload"]      = 0;
                    $trafficCols["total"]       = 0;
                    $trafficCols["ctime"]       = time();
                    $trafficCols["utime"]       = 0;

                    \App\Models\Traffics::insert($trafficCols);
                }

                //server shells
                if (!$userInfo) {
                    $username = $columnsArr["username"];
                    $password = $columnsArr["password"];
                    UserShell::createUser($username, $password);
                } else {
                    $oldPass  = $userInfo->password;
                    $username = $userInfo->username;
                    $newPasss = $columnsArr["password"];

                    if ($oldPass != $newPasss) {
                        UserShell::updateUserPassword($username, $newPasss);
                    }
                }
            });
        } catch (\Exception $err) {
            db()::rollback();
            throw $err->getMessage();
        }
    }

    public function dataTableList($pdata, $uid)
    {
        $select = [
            "users.id",
            "users.start_date",
            "users.admin_uname",
            "users.end_date",
            "users.ctime",
            "users.utime",
            "users.username",
            "users.password",
            "users.mobile",
            "users.limit_users",
            "users.traffic",
            "users.status",
            "admins.fullname as admin_name",
            "traffics.total as consumer_traffic"
        ];

        $adminRole    = getAdminRole();
        // $onlineUsers = getLocalOnlienUsers();
        $onlineUsers   = UserShell::onlineUsers();

        $query = db($this->table)->select($select)
            ->join('admins', 'admins.username', '=', 'users.admin_uname')
            ->join('traffics', 'traffics.username', '=', 'users.username')
            ->orderBy("id", "DESC");

        if ($adminRole !== "admin") {
            $query->where("admins.role", $adminRole);
        }

        if (!empty($pdata["search"]["value"])) {
            $search = $pdata["search"]["value"];
            $search = trim($search);
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where("users.username", "LIKE", "%$search%")
                        ->orWhere("users.mobile",   $search)
                        ->orWhere("users.email",    "%$search%")
                        ->orWhere("users.desc",     "%$search%")
                        ->orWhere("admins.fullname",  "LIKE", "%$search%");
                });
            }
        }
        $pdata["search"]["value"] = "";

        $DataTable      = new \App\Libraries\DataTable($query, $pdata);
        $users          = $DataTable->query()->toArray();

        $resUsers = array();
        $num = (!empty($pdata['start'])) ? $pdata['start'] : 0;

        $currentTime = time();
        foreach ($users as $user) {
            $user = (array) $user;

            $utime          = 0;
            $startDate      = 0;
            $endDate        = 0;
            $remainingDays  = 0;
            $diffrenceDate  = "";

            $uStartDate      = $user["start_date"];
            $uEndDate        = $user["end_date"];
            $username        = $user["username"];
            $usersOnline     = $onlineUsers && isset($onlineUsers[$username]) ? $onlineUsers[$username] : [];

            if ($user["utime"]) {
                $utime = Jalalian::forge($user["ctime"])->format('Y/m/d');
            }

            if ($uStartDate) {
                $startDate = Jalalian::forge($uStartDate)->format('Y/m/d');
            }

            if ($uEndDate) {
                $endDate = Jalalian::forge($uEndDate)->format('Y/m/d');
            }

            if ($uEndDate && $uStartDate) {
                $uStartDate = getStartOfDate($uStartDate);
                $uEndDate   = getEndOfDate($uEndDate);

                $diffrenceDate = calcDifferenceDate($uStartDate, $uEndDate);
                if ($user["end_date"] > $currentTime) {
                    $remainingDays  = floor(($uEndDate  - $currentTime) / 86400);
                } else {
                    $remainingDays  = -1;
                }
            }


            $num = $num + 1;
            $row = array();

            $row['id']                      = $user["id"];
            $row['idx']                     = $num;
            $row['username']                = $user["username"];
            $row['admin_name']              = $user["admin_name"];
            $row['password']                = $user["password"];
            $row['limit_users']             = $user["limit_users"];
            $row['mobile']                  = $user["mobile"];
            $row['status']                  = $user["status"];
            $row['status_label']            = userStatusLabel($user["status"]);
            $row['start_date']              = $startDate;
            $row['end_date']                = $endDate;
            $row['ctime']                   = Jalalian::forge($user["ctime"])->format('Y/m/d');
            $row['utime']                   = $utime;
            $row['traffic']                 = $user["traffic"];
            $row['consumer_traffic']        = $user["consumer_traffic"];
            $row['traffic_format']          = $user["traffic"] ? formatTraffice($user["traffic"]) : "نامحدود";
            $row['consumer_traffic_format'] = formatTraffice($user["consumer_traffic"]);
            $row['diffrence_date']          = $diffrenceDate;
            $row['remaining_days']          = $remainingDays;
            $row['online_users']            = $usersOnline;

            $resUsers[] = $row;
        }
        $result = $DataTable->make($resUsers);
        return $result;
    }

    public function isExistUsername($value, $uid = null)
    {
        $query = $this->where("username", $value);
        if ($uid != null) {
            $query->where('id', '!=', $uid);
        }
        return $query->count();
    }

    public function getInfo($userId)
    {
        $select = [
            "users.*",  "traffics.total as consumer_traffic",
            "admins.fullname as admin_name"
        ];

        $query = db($this->table)->select($select)
            ->join('traffics', 'traffics.username', '=', 'users.username')
            ->join('admins', 'admins.username', '=', 'users.admin_uname')
            ->where("users.id", $userId)
            ->get();
        if ($query->count()) {
            $row                            = $query->first();

            $remainingDays                  = 0;
            $currentTime                    = time();
            $row->is_expired                = true;
            $row->format_traffic            = $row->traffic ? formatTraffice($row->traffic) : "نامحدود";
            $row->format_consumer_traffic   = formatTraffice($row->consumer_traffic);

            if ($row->end_date) {
                $row->end_date_jd = Jalalian::forge($row->end_date)->format('Y/m/d');
            }
            if ($row->start_date) {
                $row->start_date_jd = Jalalian::forge($row->start_date)->format('Y/m/d');
            }

            if ($row->end_date && $row->start_date) {
                if ($currentTime + 1 >  $row->start_date) {
                    if ($currentTime < $row->end_date) {
                        $remainingDays  = floor(($row->end_date  - $currentTime) / 86400);
                        $row->is_expired = false;
                    } else {
                        $remainingDays  = -1;
                    }
                }
            }

            $row->remaining_days    = $remainingDays;
            $row->netmod_qr_url     = generateNetmodQR($row);
            $row->array_config      = getUserConfig($row->username, $row->password);

            return  $row;
        }
        return false;
    }

    public function getByUsername($username)
    {
        $select = [
            "users.*",  "traffics.total as consumer_traffic",
        ];

        $query = db($this->table)->select($select)
            ->join('traffics', 'traffics.username', '=', 'users.username')
            ->where("users.username", $username)
            ->get();
        if ($query->count()) {
            $row  = $query->first();
            return $row;
        }
        return false;
    }

    public function checkExist($id)
    {
        return $this->where("id", $id)->count();
    }

    public function toggleActive($userId, $uid)
    {
        $userInfo = $this->getInfo($userId);
        if ($userInfo) {

            $username = $userInfo->username;
            $password = $userInfo->password;
            $status   = $userInfo->status;

            //exec shell
            if ($status == "active") {
                $this->updateStatus($userId, "de_active");
                UserShell::deactivateUser($username);
            } else if ($status == "de_active") {
                $this->updateStatus($userId, "active");
                UserShell::activateUser($username, $password);
            }
        }
    }

    public function resetTraffic($userId, $uid)
    {
        $userInfo = $this->getInfo($userId);
        if ($userInfo) {
            \App\Models\Traffics::where("username", $userInfo->username)->update([
                "download"  => 0,
                "upload"    => 0,
                "total"     => 0,
                "utime"     => time(),
            ]);
        }
    }

    public function deleteUser($userId, $uid)
    {
        $userInfo = $this->getInfo($userId);
        if ($userInfo) {
            $username = $userInfo->username;

            $this->where("id", $userId)->delete();
            \App\Models\Traffics::where("username", $userInfo->username)->delete();

            //delete from server
            UserShell::deleteUser($username);
        }
    }

    public function totalUsers($status = null)
    {
        $query = $this->where("id", ">", 0);
        if ($status) {
            $query->where("status", $status);
        }
        return  $query->count();
    }

    public function updateExpirydates($username, $startDate, $endDate)
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        $this->where("username", $username)
            ->update(["start_date" => $startDate, "end_date" => $endDate]);
    }

    public function activeUsers()
    {
        $select = [
            "users.*",  "traffics.total as consumer_traffic",
        ];
        $query = db($this->table)->select($select)
            ->join('traffics', 'traffics.username', '=', 'users.username')
            ->where("users.status", "active")
            ->get();
        if ($query->count()) {
            return $query->toArray();
        }

        return false;
    }

    public function updateStatus($userId, $status)
    {
        $this->where("id", $userId)
            ->update(["status" => $status]);
    }


    public function adminAccessUsers()
    {
        $users = [];
        $adminUname = getAdminUsername();

        $query =  db("users")->select("username")->where("admin_uname", $adminUname)->get();
        if ($query->count()) {

            $rows = $query->toArray();

            foreach ($rows as $row) {
                $users[] = $row->username;
            }
        }

        return $users;
    }
}
