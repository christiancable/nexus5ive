# nexusfive local server setup
# cfcable
Vagrant.configure(2) do |config|
  # config.vm.box = "hashicorp/precise32"
  config.vm.box = "ubuntu/trusty64"
  config.vm.provision :shell, path: "bootstrap.sh"
  config.vm.network :forwarded_port, host: 4567, guest: 80
end
