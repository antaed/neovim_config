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

* Neovim
* `sudo apt install ripgrep`
* `sudo apt install universal-ctags`


### Step 2 - Prepare $HOME directory

```
$ cd ~
$ mkdir .config/backups/.backup .config/backups/.swp .config/.backups/.undo gutentags
$ git clone git@github.com:antaed/neovim_config.git .config/nvim
$ git clone https://github.com/k-takata/minpac.git ~/.config/nvim/pack/minpac/opt/minpac
```

### Step 3 - Install plugins

* `:call minpac#update()` 
* `:call coc#util#install()`


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

<br/>

### [Go to gVim version](https://github.com/antaed/gvim_config)
### [Go to MacVim version](https://github.com/antaed/macvim_config)

