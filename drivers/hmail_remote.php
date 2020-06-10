<?php

/**
 * hMailserver remote password driver
 *
 * @version 1.1
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

class rcube_hmail_remote_password
{
    public function save($curpass, $passwd)
    {
        $rcmail = rcmail::get_instance();

        if ($curpass == '' || $passwd == '') {
            return PASSWORD_ERROR;
        }

        $hmailRemoteUrl = $rcmail->config->get('hmailserver_remote_url',false);
        if (!$hmailRemoteUrl) {
            rcube::write_log('errors','Plugin password (hmail remote driver): $config[\'hmailserver_remote_url\'] is not defined.');
            return PASSWORD_ERROR;
        }

        $username = $rcmail->user->data['username'];
        if (strstr($username,'@')){
            $temparr = explode('@', $username);
            $domain = $temparr[1];
        }
        else {
            $domain = $rcmail->config->get('username_domain',false);
            if (!$domain) {
                rcube::write_log('errors','Plugin password (hmail remote driver): $config[\'username_domain\'] is not defined.');
                return PASSWORD_ERROR;
            }
            $username = $username . "@" . $domain;
        }

        $dataToSend = array(
            'email'       => $username,
            'oldpassword' => $curpass,
            'newpassword' => $passwd
        );
        $result = $this->remote_access($hmailRemoteUrl,$dataToSend);

        if(!is_array($result)) {
            rcube::write_log('errors', "Plugin password (hmail remote driver): ".$result);
            return PASSWORD_CONNECT_ERROR;
        }
        elseif($result['error'] != PASSWORD_SUCCESS) {
            rcube::write_log('errors', "Plugin password (hmail remote driver): ".$result['text']);
            return $result['error'];
        }

        return PASSWORD_SUCCESS;
    }

    private function remote_access($url,$data)
    {
        $data_string = http_build_query($data);

        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_string); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); 
        $response = curl_exec($ch);

        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                case 200:  # OK
                    $return = unserialize($response);
                    break;
                default:
                    $return = 'Unexpected HTTP code: ' . $http_code . ' ' . strip_tags($response);
            }
        }
        else
            $return = 'Curl error: ' . curl_error($ch);

        curl_close($ch);
        return $return;
    }

}
