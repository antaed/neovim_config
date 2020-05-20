" Remap leader
map <Space> <Leader>

" Open vimrc in new tab
nmap <silent><expr> <leader>g ":tabnew ~/.config/nvim/init\<CR>"

" Source gvimrc
nmap <leader>sg :source $MYVIMRC<CR>

" Correct pasting
nnoremap <leader>p p`[v`]=

" Copy and paste highlighted word
if (&hls && v:hlsearch)
    nnoremap <C-c> "+yiw
    nnoremap <C-v> ciw<C-r><C-o>+<esc>
endif

" Search highlights off
nnoremap <silent> <leader><space> :let @/ = expand("<cword>") <bar> :set invhlsearch<cr>

" Remap buffer motion
nnoremap <silent> <C-Down> :bnext<CR>
nnoremap <silent> <C-Up> :bprevious<CR>
nnoremap <silent> <C-Left> :tabprevious<CR>
nnoremap <silent> <C-Right> :tabnext<CR>

" Remap Ctrl-a
nnoremap <C-a> ggVG

" Switch windows
map <M-Down>  <C-W>j
map <M-Up>    <C-W>k
map <M-Left>  <C-W>h
map <M-Right> <C-W>l

" Move windows
map <S-Down>  <C-W>J
map <S-Up>    <C-W>K
map <S-Left>  <C-W>H
map <S-Right> <C-W>L

" Ease movement
nnoremap <silent> <C-j> :move+<CR>==
nnoremap <silent> <C-k> :move-2<CR>==
nnoremap <C-h> <<
nnoremap <C-l> >>
xnoremap <silent> <C-j> :move'>+<CR>gv=gv
xnoremap <silent> <C-k> :move-2<CR>gv=gv
xnoremap <C-h> <gv
xnoremap <C-l> >gv
cnoremap <C-j> <Down>
cnoremap <C-k> <Up>
cnoremap <C-h> <Left>
cnoremap <C-l> <Right>

"Duplicate lines above and below
xnoremap <leader>j YPgv
nnoremap <leader>j Yp
xnoremap <leader>k Y`>pgv
nnoremap <leader>k YP

" jump to next/previous code block
nnoremap <silent> <M-j> /\W<\zs\w\\|[([{]\zs.\\|<?php\s\zs\w<cr>:noh<cr>
nnoremap <silent> <M-k> ?\(\W<\)\@<=\w\\|\([([{]\)\@<=.\\|\(<\?php\s\)\@<=\w<cr>:noh<cr>
inoremap <silent> <M-j> <esc>l/\W<\zs\w\\|[([{]\zs.\\|<?php\s\zs\w<cr>:noh<cr>i
inoremap <silent> <M-k> <esc>?\(\W<\)\@<=\w\\|\([([{]\)\@<=.\\|\(<\?php\s\)\@<=\w<cr>:noh<cr>i

" Jump to next/previous target
nnoremap <silent> <M-l> /\w\+<cr>:noh<cr>h 
nnoremap <silent> <M-h> ?\w\+<cr>:noh<cr>h 
inoremap <silent> <M-l> <esc>l/\w\+<cr>:noh<cr>ea
inoremap <silent> <M-h> <esc>?\w\+<cr>n:noh<cr>ea

"Will open files in current directory, allows you to leave the working cd in the project root. You can also use %% anywhere in the command line to expand.
cnoremap %% <C-R>=expand('%:h').'/'<cr>
nmap <leader>ew :e %%
nmap <leader>es :sp %%
nmap <leader>ev :vsp %%
nmap <leader>et :tabe %%

" Reselect pasted block
nnoremap <expr> gp '`[' . strpart(getregtype(), 0, 1) . '`]'

" Open vertical split and switch to it
nnoremap <leader>w <C-w>v<C-w>l

" Autocomplete remaps
" Go back in insert mode
inoremap <expr> <C-h> pumvisible() ? "\<esc>a" : "\<esc>i"
" Go forward in insert mode
inoremap <expr> <C-l> pumvisible() ? "\<esc>a" : "\<esc>la"
" Delete to beginning of the line
inoremap <expr> <C-BS> pumvisible() ? "\<esc>a" : "\<C-u>"

" Replace word under cursor | within visual selection
nnoremap <leader>\ :%s/\<<C-r><C-w>\>/
vnoremap <leader>\ :s/\%V

" Shrink/Enlarge selection
vnoremap <M-j> ojo
vnoremap <M-h> ok1o
vnoremap <M-k> k$
vnoremap <M-l> j$

" Delete all but PHP / Delete all PHP
vnoremap <silent> <leader>dh J0/<?php.\{-}?><cr><esc>:call ClearAllButMatches()<cr>:noh<cr>
vnoremap <silent> <leader>dp :s/\%V<?php.\{-}?>/string/g<cr>

" Vim-Session plugin remaps
nnoremap <silent> <F9> :packadd vim-misc <bar> :packadd vim-session <bar> :echom "Vim-Sessions added"<cr>
nnoremap <leader>so :OpenSession
nnoremap <leader>ss :SaveSession
nnoremap <leader>sd :DeleteSession<CR>
nnoremap <leader>sc :CloseSession<CR>

" Delete multiple spaces between tags
nnoremap <silent> <leader>ds vat :s/\%V\(\s\+\ze<\\|>\zs\s\+\)//g <bar> :noh<cr>gv=
vnoremap <silent> <leader>ds :s/\%V\(\s\+\ze<\\|>\zs\s\+\)//g <bar> :noh<cr>gv=

" Clear trailing spaces
nnoremap <silent> <leader>cs :let _s=@/ <Bar> :%s/\s\+$//e <Bar> :let @/=_s <Bar> :nohl <Bar> :unlet _s <CR>

" Clear empty liness
nnoremap <silent> <leader>de vat :g/^\s*$/d <bar> :noh<cr>
vnoremap <silent> <leader>de :g/^\s*$/d <bar> :noh<cr>

" Split tags/braces into separate lines
nnoremap <silent> <M-t> cit<esc>O<esc>p==
nnoremap <silent> <M-b> /[([{]<cr>v%<esc>i<esc>`<a<esc>:noh<cr>
autocmd FileType php  nnoremap <silent> <M-p> 0f{V%<esc>?<?<cr>hv`</?><cr>llc<esc>:noh<cr>O<esc>p==

" Renamer
nmap <Leader>r <Plug>RenamerStart

" Improved line/selection Surround
nmap <silent> <M-w> ysstdiv.<CR>F"i
vmap <silent> <M-w> V<esc>gvStdiv.<CR>vat=?""<CR>a

" Delete spaces between tags when joining lines
autocmd FileType php,html noremap <silent> J J^v$:s/\%V\(\s\+\ze<\\|>\zs\s\+\)//g <bar> :noh<cr>$

" Comment inside php tags
noremap <leader>ci :s/<?php[\n\r\s]*\zs\(.\{-}\)\ze[\n\r\s]*?>/\/*\1*\//g <bar> :noh<cr>
vnoremap <leader>ci :s/\%V<?php[\n\r\s]*\zs\(.\{-}\)\ze[\n\r\s]*?>/\/*\1*\//g <bar> :noh<cr>

" Delete comments inside php tags
noremap <leader>cci :s/\/\*\\|\*\///g <bar> :noh<cr>
vnoremap <leader>cci :s/\%V\/\*\\|\*\///g <bar> :noh<cr>

" Activate HiLinkTrace
nnoremap <silent> <leader>synt :packadd vim-HiLinkTrace<cr>:HLT!<cr>

" Increment numbers
noremap <A-x> <C-A>

" Load unicode.vim
nnoremap <silent> <leader>dig :packadd unicode.vim<cr>

" Increment multiple lines by one
vnoremap <leader>+ 1g<C-a>

" Multiply html tag
nnoremap <leader>t vatYgv<esc>jP

" Multiply curly brackets surrounded
nnoremap <leader>b va{Ygv<esc>jP

" Jump to indented new line from within brackets
imap <C-cr> <cr><cr><esc>ki<C-t> 
imap <expr> <CR> delimitMate#WithinEmptyPair() ? "\<Plug>delimitMateCR" : "\<cr>"

" Duplicate current buffer in the same directory
nnoremap <leader>df :sav! %:h/

" Improved line/selection comment Surround
nnoremap <leader>ca V%<esc>`<O<?php /*<esc>`>o*/ ?><esc>
nnoremap <leader>cca 0f*V%<esc>`<dd`>dd
vnoremap <leader>ca <esc>`<O<?php /*<esc>`>o*/ ?><esc>
vnoremap <leader>cca <esc>`<dd`>dd

" Delete surrounding php braces
nnoremap <leader>db va{V<esc>?<?<CR>?\S<cr>d`>`<^d/?><cr>xd/\S<cr>:noh<cr>

" wysiwyg formatting
nnoremap <silent> <leader>fs cit<span style="font-size:12px">"</span><esc>
nnoremap <silent> <leader>cn :%s/\(&nbsp;\)\+/ /g<cr>:g/<p>\s\+<\/p>/normal! dd<cr>
vnoremap <silent> <leader>ul :s/\%V<\/\{-}\zsp\ze>/li/g<cr>gv<esc>o</ul><esc>'<O<ul><esc>vat=
nnoremap <silent> <leader>bb 0i<tr><td class="bold"><esc>Ea</td><td>A</td></tr><esc>j
nnoremap <silent> <leader>lr 0cf><div class="row"><div class="col-50"><esc>/\s\{3,}<cr>cw</div><div class="col-50 text-right"><esc>/<\/<cr>C</div></div><esc>:noh<cr>
vnoremap <silent> <leader>trd Vc<tr>"</tr><esc>vit:s#\%V\zs\S.*\ze$#<td class="numeric-cell">&</td>#e<cr>:noh<cr>
vnoremap <silent> <leader>trh Vc<tr>"</tr><esc>vit:s#\%V\zs\S.*\ze$#<th class="numeric-cell">&</th>#e<cr>:noh<cr>
nnoremap <silent> <leader>td ^c$<td class="numeric-cell">"</td><esc>
nnoremap <silent> <leader>th ^c$<th class="numeric-cell">"</th><esc>
vnoremap <silent> <leader>sup c<sup>"</sup><esc>
vnoremap <silent> <leader>sub c<sub>"</sub><esc>

" Diff controls
nnoremap <leader>vd :windo diffthis<CR>
nnoremap <expr> <S-h> &diff ? '<ScrollWheelLeft>' : 'H'
nnoremap <expr> <S-l> &diff ? '<ScrollWheelRight>' : 'L'
nnoremap <expr> du &diff ? ':diffupdate' : ''
nnoremap <expr> dj &diff ? ']c' : ''
nnoremap <expr> dk &diff ? '[c' : ''

" Terminal mappings
tnoremap <esc> <C-w>N
tnoremap <F3> <C-w><C-c>
nnoremap <F3> i<C-w><C-c>

" Jump to next error
nmap <silent> <F4> <Plug>(coc-diagnostic-next-error)
nmap <silent> <S-F4> <Plug>(coc-diagnostic-prev-error)

" Compare current buffer against the file
nnoremap <silent> <expr> <F5> &diff ? ':windo diffoff:bd' : ":DiffSaved\<CR>"

" HL on double click
nnoremap <silent> <2-LeftMouse> :let @/='\V\<'.escape(expand('<cword>'), '\').'\>' <bar> :set hls<cr>

" FZF word under cursor
nmap <silent><F7> :call fzf#vim#files('.', {'options':'--query '.expand('<cword>')})<CR>
vmap <silent><F7> y:call fzf#vim#files('.', {'options':'--query '.expand('<cword>')})<CR>

" Switch projects
nnoremap <F2> :call SetProject()<CR>

" Get PHP Variables
nnoremap <leader>pv /\$\w\+<CR>:CopyMatches<CR>:vnew<CR>:vertical resize 80<CR>"+p:sort u<CR>:nohl<CR>dd
vnoremap <leader>pv <esc>/\%V\$\w\+<CR>:CopyMatches<CR>:vnew<CR>:vertical resize 80<CR>"+p:sort u<CR>:nohl<CR>dd

" Activate Goyo
nnoremap <silent> <expr> <F6> exists('#goyo') ? ":Goyo!\<cr>" : ":packadd goyo.vim \<bar> :Goyo\<cr>"

" Toggle colorscheme
nnoremap <silent> <expr> <F10> exists('#goyo') ? ":call ToggleColorscheme()<CR>:source $MYVIMRC<CR>:packadd goyo.vim \<bar> :Goyo\<cr>" :  ":call ToggleColorscheme()<CR>:source $MYVIMRC<CR>"

" FZF mappings
nnoremap <silent> <C-f> :call fzf#vim#files('.', {'options': '--prompt ""'})<CR>
nnoremap <silent> <C-b> :Buffers<CR>
nnoremap <silent> <C-t> :History<CR>
nnoremap <silent> <C-g> :RG<CR>

" Exit terminal mode
tnoremap <Esc> <C-\><C-n>

" Fix switching to prev buffer
noremap <C-p> <C-^>

" Floaterm
nnoremap   <silent>   <F12>   :FloatermToggle<CR>
tnoremap   <silent>   <F12>   <C-\><C-n>:FloatermToggle<CR>
