<?php
    class MailManager extends Model
    {
        public function __construct()
        {
            parent::__construct('mail_manager');
        }

        /**
         * @return bool Whether the server can send a mail today.
         */
        private function can_send_mail()
        {
            $current_time = time();
            $current_date = strftime("%Y-%m-%d", $current_time);
            $today = strtotime($current_date);

            $sql_result = $this->execute_request('SELECT * FROM mail_manager WHERE date=:date', array(':date' => $today));

            if ($result = $sql_result->fetchArray(SQLITE3_ASSOC))
            {
                if ($result['mails_sent'] > Configuration::get('max_mails_per_day') - 1)
                {
                    return false;
                }
                // update the mails sent count
                $this->execute_request('UPDATE mail_manager SET mails_sent=:mails_sent WHERE id=:id',
                array(
                    ':id' => $result['id'],
                    ':mails_sent' => ($result['mails_sent'] + 1)
                ));

                return true;
            }
            else
            {
                $this->execute_request('INSERT INTO mail_manager (date, mails_sent) VALUES (' . $today . ', 1)');
                return true;
            }
        }

        private function escape_quotes(string $string)
        {
            $result = escapeshellarg($string);
            return $result;

            //return preg_replace('/([^\'])[\']([^\'])/', '$1T$2', $result);
        }

        /**
         * @param to The receiver.
         * @param subject The mail subject.
         * @param message The mail content.
         * @param headers The mail headers.
         * 
         * @return bool Whether the mail has been added to the send queue.
         */
        public function send_mail($to, $subject, $message, $headers)
        {
            if (!$this->can_send_mail())
            {
                return;
            }

            $command_string = '/usr/bin/python "';
            $command_string .= Configuration::get('mail_executable_path') . '" "';
            $command_string .= Configuration::get('mail_credentials_path');
            $command_string .= '" ' . $this->escape_quotes($to);
            $command_string .= ' ' . $this->escape_quotes($subject);
            $command_string .= ' ' . $this->escape_quotes($message);
            $command_string .= ' ' . $this->escape_quotes($headers);

            $command = escapeshellcmd($command_string);
            //exec($command);
        }
    }
?>