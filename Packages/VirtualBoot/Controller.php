<?php

namespace VirtualBoot;

use App;

class Controller extends App\Controller 
{
    public function install($request){
        $package = $request->get('name');
        try {
            $this->VirtualBoot->install($package);
            $this->Output->reply("Package " . $package . " installed");
        } catch (\Exception $e) {
            $this->Output->reply($e->getMessage());
        }
    }
    public function uninstall($request){
        $package = $request->get('name');
        try {
            $this->VirtualBoot->uninstall($package);
            $this->Output->reply("Package " . $package . " uninstalled");
        } catch (\Exception $e) {
            $this->Output->reply($e->getMessage());
        }
    }
    public function activate($request){
        $package = $request->get('name');
        try {
            $this->VirtualBoot->activate($package);
            $this->Output->reply("Package " . $package . " activated");
        } catch (\Exception $e) {
            $this->Output->reply($e->getMessage());
        }
    }
    public function deactivate($request){
        $package = $request->get('name');
        try {
            $this->VirtualBoot->deactivate($package);
            $this->Output->reply("Package " . $package . " deactivated");
        } catch (\Exception $e) {
            $this->Output->reply($e->getMessage());
        }
    }
}