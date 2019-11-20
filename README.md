# My MacVim config

![screenshot](https://repository-images.githubusercontent.com/221780628/b6f09b00-0940-11ea-9447-f21ae15e3c42)

This repository contains my MacVim configuration process for a new computer, it is based on my own work flow as a web developer (in PHP, JavaScript, HTML, CSS) and it includes:

* my **gvimrc** file
* **.vim** directory containing various customization files including my own theme, snippets, syntax highlighting, etc.
* the font of my choice

**Disclaimer**\
*I created this repository for my own needs and I highly recommend that you check it thoroughly before installing it on your system. I am not responsible if something goes wrong, so use at your own risk.*


## INSTALLATION

### Step 1 - Install prequisites

* [MacVim](https://github.com/macvim-dev/macvim/releases/tag/snapshot-161)
* [Homebrew](https://brew.sh/)
* [Karabiner Elements](https://pqrs.org/osx/karabiner/)
* [Add Karabiner rules](https://pqrs.org/osx/karabiner/complex_modifications/) - Change caps_lock key (rev 4+) & Change shift key (rev 2+)
* `brew install ripgrep`
* `brew install universal-ctags`


### Step 2 - Prepare $HOME directory

```
$ cd ~
$ mkdir .backups/.backup .backups/.swp .backups/.undo gutentags
$ git clone git@github.com:antaed/macvim_config.git .vim
$ git clone https://github.com/k-takata/minpac.git ~/.vim/pack/minpac/opt/minpac
$ echo "runtime gvimrc" > .gvimrc
```
* Install one of the font variants from *~/.vim/antaed*


### Step 3 - Install plugins

* `:call minpac#update()` 
* `:call coc#util#install()`


### Step 4 - Set MacVim as git mergetool

```
$ git config --global merge.tool diffconflicts
$ git config --global mergetool.diffconflicts.cmd "macvim -c DiffConflicts \"$MERGED\" \"$BASE\" \"$LOCAL\" \"$REMOTE\""
$ git config --global mergetool.diffconflicts.trustExitCode true
$ git config --global mergetool.keepBackup false
```

### Step 5 - Set MacVim preferences

* When MacVim launches: Check for updates
* Open files from applications: in a new window and set the arglist
* After last window closes: Hide MacVim


### If issues...

* check *~/.config/* permissions
* open files that won't load with MacVim and run `:w ++ff=unix`

<br/>

### [Go to gVim version](https://github.com/antaed/gvim_config)

