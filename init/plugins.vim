call plug#begin(stdpath('data').'/plugged')
Plug 'neoclide/coc.nvim', {'branch': 'release'} " :CocInstall coc-css coc-html coc-json coc-tsserver coc-phpls coc-vimlsp coc-tailwindcss
Plug 'tpope/vim-vinegar'
Plug 'itchyny/lightline.vim'
Plug 'marcweber/vim-addon-mw-utils'
Plug 'tomtom/tlib_vim'
Plug 'honza/vim-snippets'
Plug 'garbas/vim-snipmate'
Plug 'godlygeek/tabular'
Plug 'Raimondi/delimitMate'
Plug 'vim-scripts/CSSMinister'
Plug 'tomtom/tcomment_vim'
Plug 'tpope/vim-repeat'
Plug 'tpope/vim-unimpaired'
" Plug 'ludovicchabant/vim-gutentags'
Plug 'captbaritone/better-indent-support-for-php-with-html'
Plug 'coderifous/textobj-word-column.vim'
Plug 'qpkorr/vim-renamer'
Plug 'junegunn/vim-slash'
Plug 'tpope/vim-abolish'
Plug 'machakann/vim-sandwich'
Plug 'whiteinge/diffconflicts'
Plug 'stefandtw/quickfix-reflector.vim'
Plug 'StanAngeloff/php.vim'
Plug 'junegunn/fzf', { 'do': { -> fzf#install() } }
Plug 'junegunn/fzf.vim'
Plug 'norcalli/nvim-colorizer.lua'
Plug 'voldikss/vim-floaterm'
Plug 'junegunn/goyo.vim', {'on': 'Goyo'}
Plug 'jwalton512/vim-blade'
Plug 'gerw/vim-HiLinkTrace'
call plug#end()


"FZF Config
let g:i = 0
let g:fzf_preview_window = ''
autocmd! FileType fzf call FzfOptions()
function! FzfOptions()
    set laststatus=0 noshowmode noruler
    autocmd BufLeave <buffer> set laststatus=2 showmode ruler
    tnoremap <buffer> <esc> <c-c>
    tnoremap <buffer><silent> <C-l> :<C-\><C-n>:close<CR>:sleep 100m<CR>:<C-u>call FzfToggler(g:i, 0)<CR>
    tnoremap <buffer><silent> <C-h> :<C-\><C-n>:close<CR>:sleep 100m<CR>:<C-u>call FzfToggler(g:i, 1)<CR>
endfunction
function! FzfToggler(i, j)
    let modes = ['Files', 'Buffers', 'History']
    if (a:j==0)
        let g:i = a:i >= 0 && a:i < len(modes)-1 ? a:i + 1 : 0
    else
        let g:i = a:i > 0 && a:i <= len(modes) ? a:i - 1 : len(modes) - 1
    endif
    exe ':' . modes[g:i]
endfunction
" floating window
let $FZF_DEFAULT_OPTS='--layout=reverse  --margin=1,3'
let g:fzf_layout = { 'window': 'call FloatingFZF(0.6, 0.6)' }
function! FloatingFZF(w,h)
  let buf = nvim_create_buf(v:false, v:true)
  call setbufvar(buf, '&signcolumn', 'no')
  let width = float2nr(&columns * a:w)
  let height = float2nr(&lines * a:h)
  let horizontal = float2nr((&columns - width) / 2)
  let vertical = float2nr((&lines - height) / 2)
  let opts = { 'relative': 'editor', 'row': vertical, 'col': horizontal, 'width': width, 'height': height, 'style': 'minimal' }
  call nvim_open_win(buf, v:true, opts)
endfunction
let g:fzf_colors = { 
  \ 'fg':         ['fg', 'Ignore'],
  \ 'fg+':        ['fg', 'Ignore'],
  \ 'bg':         ['bg', 'PMenu'],
  \ 'border':     ['bg', 'PMenu'],
  \ 'gutter':     ['bg', 'PMenu'],
  \ 'hl':         ['fg', 'WildMenu'],
  \ 'prompt':     ['fg', 'WildMenu'],
  \ 'marker':     ['fg', 'WildMenu'],
  \ 'header':     ['fg', 'WildMenu'],
  \ 'preview-bg': ['bg', 'WildMenu'],
  \ 'bg+':        ['fg', 'Cursor'],
  \ 'pointer':    ['fg', 'Cursor'],
  \ 'info':       ['fg', 'Comment'],
  \ 'spinner':    ['fg', 'Comment'],
  \ 'preview-fg': ['fg', 'Normal'],
  \ 'hl+':        ['fg', 'Search'] }
" FZF Rg config
function! RipgrepFzf(query)
  let g:fzf_layout = { 'window': 'call FloatingFZF(0.8, 0.8)' }
  let command_fmt = "rg --column --line-number --no-heading --color=always --colors 'line:none' --colors 'path:none' --colors 'match:none' --colors match:'fg:".(g:colors_name=='antaed' ? 'red' : 'black')."' --smart-case -- %s || true"
  let initial_command = printf(command_fmt, shellescape(a:query))
  let reload_command = printf(command_fmt, '{q}')
  let spec = {'options': ['--phony', '--query', a:query, '--bind', 'change:reload:'.reload_command, '--preview-window', 'noborder']}
  call fzf#vim#grep(initial_command, 1, fzf#vim#with_preview(spec))
  let g:fzf_layout = { 'window': 'call FloatingFZF(0.6, 0.6)' }
endfunction
command! -nargs=* -bang RG call RipgrepFzf(<q-args>)

" Session management
let g:session_autoload = "no"
let g:session_autosave = "no"
let g:session_command_aliases = 1

" SnipMate parser version
let g:snipMate = {}
let g:snipMate.snippet_version = 1
" Enable php, blade snippets in html
au BufRead,BufNewFile *.html set ft=html.php
au BufRead,BufNewFile *.blade.php set ft=blade.php

" Gutentags config
" let g:gutentags_ctags_executable = '~/ctags/ctags.exe'
" let g:gutentags_cache_dir = '~/gutentags/'
" function! GetPwd(path) abort
"     return getcwd()
" endfunction
" let g:gutentags_project_root_finder='GetPwd'
" let g:gutentags_add_default_project_roots=0

" Save Renamer buffer with :w
let g:RenamerSupportColonWToRename = 1

" Disable intrusive CSSMinister mapping
autocmd VimEnter * unmap <leader>ha

" Enable demilitMate space expansion
let delimitMate_expand_space = 1

" Blink cursor after search
if has('timers')
  noremap <expr> <plug>(slash-after) 'zz'.slash#blink(2, 50)
endif
" autocmd! slash BufEnter *

" coc.nvim configuration
set hidden
set signcolumn=yes
let g:coc_snippet_next = '<Tab>'
let g:coc_snippet_prev = '<S-Tab>'
let g:coc_node_path = '/home/antaed/.nvm/versions/node/v12.16.3/bin/node'
" Correct php variable
autocmd FileType php setl iskeyword+=$
autocmd! Completedone * if pumvisible() == 0 | pclose | endif
" Use command ':verbose imap <tab>' to make sure tab is not mapped by other plugin.
inoremap <silent><expr> <C-j>
      \ pumvisible() ? "\<C-n>" :
      \ <SID>check_back_space() ? "\<C-j>" :
      \ coc#refresh()
inoremap <expr><C-k> pumvisible() ? "\<C-p>" : "\<C-h>"
function! s:check_back_space() abort
  let col = col('.') - 1
  return !col || getline('.')[col - 1]  =~# '\s'
endfunction
" Use <cr> for confirm completion, `<C-g>u` means break undo chain at current position.
inoremap <expr> <cr> pumvisible() ? "\<C-y>" : "\<C-g>u\<CR>"

" Vim-Sandwich - enable vim-surround mappings
runtime macros/sandwich/keymap/surround.vim
" Text-objects
xmap is <Plug>(textobj-sandwich-query-i)
xmap as <Plug>(textobj-sandwich-query-a)
omap is <Plug>(textobj-sandwich-query-i)
omap as <Plug>(textobj-sandwich-query-a)
xmap iss <Plug>(textobj-sandwich-auto-i)
xmap ass <Plug>(textobj-sandwich-auto-a)
omap iss <Plug>(textobj-sandwich-auto-i)
omap ass <Plug>(textobj-sandwich-auto-a)
xmap im <Plug>(textobj-sandwich-literal-query-i)
xmap am <Plug>(textobj-sandwich-literal-query-a)
omap im <Plug>(textobj-sandwich-literal-query-i)
omap am <Plug>(textobj-sandwich-literal-query-a)
xmap ip isp
xmap ap asp
omap ip isp
omap ap asp
xmap il isl
xmap al asl
omap il isl
omap al asl
" if you have not copied default recipes
let g:sandwich#recipes = deepcopy(g:sandwich#default_recipes)
" add spaces inside braket
let g:sandwich#recipes += [
      \   {'buns': ['{ ', ' }'], 'nesting': 1, 'match_syntax': 1, 'kind': ['add', 'replace'], 'action': ['add'], 'input': ['{']},
      \   {'buns': ['[ ', ' ]'], 'nesting': 1, 'match_syntax': 1, 'kind': ['add', 'replace'], 'action': ['add'], 'input': ['[']},
      \   {'buns': ['( ', ' )'], 'nesting': 1, 'match_syntax': 1, 'kind': ['add', 'replace'], 'action': ['add'], 'input': ['(']},
      \   {'buns': ['{\s*', '\s*}'],   'nesting': 1, 'regex': 1, 'match_syntax': 1, 'kind': ['delete', 'replace', 'textobj'], 'action': ['delete'], 'input': ['{']},
      \   {'buns': ['\[\s*', '\s*\]'], 'nesting': 1, 'regex': 1, 'match_syntax': 1, 'kind': ['delete', 'replace', 'textobj'], 'action': ['delete'], 'input': ['[']},
      \   {'buns': ['(\s*', '\s*)'],   'nesting': 1, 'regex': 1, 'match_syntax': 1, 'kind': ['delete', 'replace', 'textobj'], 'action': ['delete'], 'input': ['(']},
      \   {'buns': ['<?php\s*', '\s*?>'], 'nesting': 1, 'regex': 1, 'kind': ['textobj'], 'filetype': ['php'], 'input': ['p']},
      \   {'buns': ['^\s*', '\s*$'], 'regex': 1, 'linewise': 1, 'kind': ['textobj'], 'input': ['l']},
      \ ]

" Blade
" Define some single Blade directives. This variable is used for highlighting only.
" let g:blade_custom_directives = ['datetime', 'javascript']
" Define pairs of Blade directives. This variable is used for highlighting and indentation.
" let g:blade_custom_directives_pairs = {
"       \   'markdown': 'endmarkdown',
"       \   'cache': 'endcache',
"       \ }
