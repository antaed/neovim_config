let g:python_host_prog  = '/usr/bin/python2.7'
let g:python3_host_prog = '/usr/bin/python3.8'

" Defaults
set mouse=a
set clipboard=unnamedplus
set undofile
set tabstop=4 softtabstop=4 shiftwidth=4 expandtab breakindent autoindent
set laststatus=2 noshowmode " Because lightline won't show up without this | Not necessary if powerline is installed
set relativenumber number
set ignorecase smartcase
set wildmenu showmatch
set incsearch hlsearch
set shellslash " set forward slash when expanding file paths
set diffopt=filler,foldcolumn:0,context:0
set notimeout ttimeout timeoutlen=100 " prevent esc key mapping clashes in terminal
set redrawtime=10000
set updatetime=750
set wildignore+=*/.git/*,*/tmp/*,*.swp
set encoding=utf-8
set nofixendofline
set mmp=2000
scriptencoding utf-8
filetype plugin indent on

" Netrw configuration
let g:netrw_liststyle = 0
let g:netrw_browse_split = 4
let g:netrw_altv = 1
let g:netrw_preview = 1
let g:netrw_winsize = 25
let g:netrw_ftp_cmd="ftp -p"

" Set default working directory
" tcd ~/www/

source ~/.config/nvim/init/toggler.vim
source ~/.config/nvim/init/theme.vim
source ~/.config/nvim/init/plugins.vim
source ~/.config/nvim/init/scripts.vim
source ~/.config/nvim/init/keymaps.vim
source ~/.config/nvim/init/projects.vim

lua require 'colorizer'.setup()
