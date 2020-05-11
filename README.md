# My Neovim (on WSL2) config

![screenshot](https://repository-images.githubusercontent.com/221780628/b6f09b00-0940-11ea-9447-f21ae15e3c42)

This repository contains my Neovim configuration process for a new computer, it is based on my own work flow as a web developer (in PHP, JavaScript, HTML, CSS) and it includes:

* my **init.vim** file
* **nvim** directory containing various customization files including my own theme, snippets, syntax highlighting, etc.
* the font of my choice

**Disclaimer**\
*I created this repository for my own needs and I highly recommend that you check it thoroughly before installing it on your system. I am not responsible if something goes wrong, so use at your own risk.*


## INSTALLATION

### Step 1 - Install prequisites

**Windows**
* Chocolatey
* Neovim via Chocolatey (for [clipboard integration](https://github.com/neovim/neovim/wiki/FAQ#where-should-i-put-my-config-vimrc))
* AutoHotKey
* M+ 1mn font

**Ubuntu**
* `sudo apt install neovim`
* `sudo apt install ripgrep`
* `sudo apt install universal-ctags`
* `sudo apt install make`
* `sudo apt install p7zip-full p7zip-rar`
* `sudo apt install subversion`
* [nodejs](https://docs.microsoft.com/en-us/windows/nodejs/setup-on-wsl2)
* [golang](https://github.com/golang/go/wiki/Ubuntu)
* [LAMP](https://www.linuxbabe.com/ubuntu/install-lamp-stack-ubuntu-20-04-server-desktop)


### Step 2 - Prepare $HOME directory

```
$ cd ~
$ mkdir gutentags www
$ git clone https://github.com/antaed/neovim_config .config/nvim
$ sh -c 'curl -fLo "${XDG_DATA_HOME:-$HOME/.local/share}"/nvim/site/autoload/plug.vim --create-dirs \
       https://raw.githubusercontent.com/junegunn/vim-plug/master/plug.vim'
```

### Step 3 - Install plugins

* `:PlugInstall`
* `:CocInstall {coc-* extensions}`


### Step 4 - Set Neovim as git mergetool

```
$ git config --global merge.tool diffconflicts
$ git config --global mergetool.diffconflicts.cmd "neovim -c DiffConflicts \"$MERGED\" \"$BASE\" \"$LOCAL\" \"$REMOTE\""
$ git config --global mergetool.diffconflicts.trustExitCode true
$ git config --global mergetool.keepBackup false
```

### If issues...

* check *~/.config/* permissions
* open files that won't load with Neovim and run `:w ++ff=unix`
* [localhost not working](https://www.bleepingcomputer.com/news/security/wsl2-now-supports-localhost-connections-from-windows-10-apps/)

<br/>

### [Go to gVim version](https://github.com/antaed/gvim_config)
### [Go to MacVim version](https://github.com/antaed/macvim_config)

