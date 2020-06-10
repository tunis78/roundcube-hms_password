<?php

/**
 * hMailserver remote password changer
 *
 * @version 1.2
 * @author Andreas Tunberg <andreas@tunberg.com>
 *
 * Copyright (C) 2017, Andreas Tunberg
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 */

$rc_remote_ip = 'YOUR ROUNDCUBE IP ADDRESS';

/*****************/

if($_SERVER['REMOTE_ADDR'] !== $rc_remote_ip)
{
    header('HTTP/1.0 403 Forbidden');
    exit('You are forbidden!');
}

define('HMS_ERROR',1);

if(empty($_POST['email']) || empty($_POST['oldpassword']) || empty($_POST['newpassword']))
    sendResult('Required fields can not be empty.',HMS_ERROR);

$email = $_POST['email'];
$oldpassword = $_POST['oldpassword'];
$newpassword = $_POST['newpassword'];


saveNewPassword($email,$oldpassword,$newpassword);

sendResult('Password changed');


function sendResult($message,$error=0)
{
    $out=array('error'=>$error,'text'=>$message);
    exit(serialize($out));
}

function saveNewPassword($email,$oldpassword,$newpassword)
{
    try {
        $obApp = new COM("hMailServer.Application");
    }
    catch (Exception $e) {
        sendResult(trim(strip_tags($e->getMessage())),HMS_ERROR);
    }
    $temparr = explode('@', $email);
    $domain = $temparr[1];
    $obApp->Authenticate($email, $oldpassword);
    try {
        $obDomain  = $obApp->Domains->ItemByName($domain);
        $obAccount = $obDomain->Accounts->ItemByAddress($email);
        $obAccount->Password = $newpassword;
        $obAccount->Save();
    }
    catch (Exception $e) {
        sendResult(trim(strip_tags($e->getMessage())),HMS_ERROR);
    }
}
