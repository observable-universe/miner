# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "debian/jessie64"

  config.vm.network "private_network", ip: "192.168.20.20"

  config.vm.provision :shell, inline: "apt-get update && apt-get install -y vim"
  config.vm.provision :docker
  config.vm.synced_folder ".", "/vagrant", type: 'nfs'
  config.vm.provision :docker_compose, run: "always", yml: "/vagrant/provision/docker-compose.yml"

  # Virtual box settings
  config.vm.provider "virtualbox" do |vb|
  	vb.gui = false
  	vb.memory = "2048"
    vb.name = "miner"
  end
end
