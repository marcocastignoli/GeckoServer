<?php

namespace tbban;

class Gecko
{
    function __construct()
    {
        \App\Kernel::implementComponents($this, 'Database');
    }

    public function install()
    {
        $this->Database->query("CREATE TABLE tbban (
            TBBAN_ID int(11) NOT NULL AUTO_INCREMENT,
            CD_ABI varchar(10) NOT NULL,
            CD_CAB varchar(10) NOT NULL,
            DES_ABI varchar(50) NOT NULL,
            DES_CAB varchar(40) NULL,
            IND_CAB varchar(50) NULL,
            LOC_CAB varchar(50) NULL,
            PRO_CAB varchar(2) NULL,
            CAP_CAB varchar(5) NULL,
            TEL_CAB varchar(20) NULL,
            NOTE1 varchar(50) NULL,
            NOTE2 varchar(50) NULL,
            PRIMARY KEY (TBBAN_ID)
        );");
        return true;
    }

    public function uninstall()
    {
        $this->Database->query("DROP TABLE tbban");
    }
}