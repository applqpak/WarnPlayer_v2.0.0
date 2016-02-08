<?php

  namespace WarnPlayer;

  use pocketmine\plugin\PluginBase;
  use pocketmine\event\Listener;
  use pocketmine\utils\TextFormat as TF;
  use pocketmine\Player;
  use pocketmine\command\Command;
  use pocketmine\command\CommandSender;

  class Main extends PluginBase implements Listener {

    public function dataPath() {

      return $this->getDataFolder();

    }

    public function onEnable() {

      $this->getServer()->getPluginManager()->registerEvents($this, $this);

      if(!(file_exists($this->dataPath()))) {

        @mkdir($this->dataPath());

        chdir($this->dataPath());

        @mkdir("Players/", 0777, true);

        touch("config.yml");

        file_put_contents("config.yml", "action_after_three_warns: kick");

      }

    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {

      if(strtolower($cmd->getName()) === "warn") {

        if(!(isset($args[0] and isset($args[1]))) {

          $sender->sendMessage(TF::RED . "Error: not enough args. Usage: /warn <player> < reason >");

          return true;

        } else {

          $sender_name = $sender->getName();

          $name = $args[0];

          $player = $this->getServer()->getPlayer($name);

          if($player === null) {

            $sender->sendMessage(TF::RED . "Player " . $name . " could not be found.");

            return true;

          } else {

            unset($args[0]);

            $reason = implode(" ", $args);
