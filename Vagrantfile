## use this using vagrant like this "VAGRANT_VAGRANTFILE=Vagrantfile.simple vagrant up"
$vagrant_ip = "192.168.33.14"
$vagrant_memory = "2048"
$vagrant_cpus = "2"
$vagrant_gui = false
$vagrant_name = "thunder"

dir = File.dirname(File.expand_path(__FILE__))
localfile = "#{dir}/Vagrantfile.local"
load localfile if File.exist? localfile

Vagrant.configure(2) do |config|

  config.vm.box = "ubuntu/trusty64"
  # xenial has a bug that should be fixed on 06-02-2016
  # config.vm.box = "ubuntu/xenial64"
  config.vm.hostname = "#{$vagrant_name}.dev.local"

  config.vm.network "private_network", ip: $vagrant_ip
  config.vm.synced_folder ".", "/vagrant_data", type: "nfs"

  config.vm.provider "virtualbox" do |vb|
    vb.gui = $vagrant_gui
    vb.memory = $vagrant_memory
    vb.cpus = $vagrant_cpus
  end

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  config.vm.provision "shell" do |script|
    script.path = "provision/shell.sh"
    script.privileged = true
    script.args = [$vagrant_name]
  end
end
