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

        if(!(isset($args[0]) and isset($args[1]))) {

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

            $player_name = $player->getName();

            if(!(file_exists($this->dataPath() . "Players/" . strtolower($player_name) . ".txt"))) {

              touch($this->dataPath() . "Players/" . strtolower($player_name) . ".txt");

              file_put_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt", "0");

            }

            $reason = implode(" ", $args);

            $file = file_get_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt");

            if($file >= "3") {

              $string = "action_after_three_warns: ";

              $action = substr(strstr(file_get_contents($this->dataPath() . "config.yml"), $string), strlen($string));

              if($action === "kick") {

                $player->kick("You were kicked for being warned 3+ times.");

                $sender->sendMessage(TF::GREEN . $player_name . " was kicked for being warned 3+ times.");

                return true;

              } else if($action === "ban") {

                $player->setBanned(true);

                $sender->sendMessage(TF::GREEN . $player_name . " was banned for being warned 3+ times.");

                return true;

              } else {

                $this->getServer()->getLogger()->error($action . " in file config.yml is invalid, valid options: kick, ban. Disabling plugin.");

                $this->getServer()->getPluginManager()->disablePlugin($this->getServer()->getPluginManager()->getPlugin("WarnPlayer"));

                return true;

              }

            } else {

              $player->sendMessage(TF::YELLOW . "You have been warned by " . $sender_name . " for " . $reason);

              $this->getServer()->broadcastMessage(TF::YELLOW . $player_name . " was warned by " . $sender_name . " for " . $reason);

              $file = file_get_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt");

              file_put_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt", $file + 1);

              $sender->sendMessage(TF::GREEN . "Warned " . $player_name . ", and added +1 warns to their file.");

              return true;

            }

          }

        }

      }

      if(strtolower($cmd->getName()) === "warns") {

        if(!(isset($args[0]))) {

          $sender->sendMessage(TF::RED . "Error: not enough args. Usage: /warns <player>");

          return true;

        } else {

          $name = $args[0];

          $player = $this->getServer()->getPlayer($name);

          if($player === null) {

            $sender->sendMessage(TF::RED . "Player " . $name . " could not be found.");

            return true;

          } else {

            $player_name = $player->getName();

            if(!(file_exists($this->dataPath() . "Players/" . strtolower($player_name) . ".txt"))) {

              $sender->sendMessage(TF::RED . $player_name . " has no warns.");

              return true;

            } else {

              $player_warns = file_get_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt");

              $sender->sendMessage(TF::GREEN . "Player " . $player_name . " has " . $player_warns . " warns.");

              return true;

            }

          }

        }

      }

    }

  }

?>
