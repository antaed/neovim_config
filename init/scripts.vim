" Toggle color scheme
function! ToggleColorscheme() abort
    exe ":colorscheme ".g:colors_name_negative
    if  exists('#goyo') 
        exe "silent! call lightline#disable()"
    endif
    exe ":e ~/.config/nvim/init/toggler.vim" | exe "normal! Di colorscheme ".g:colors_name_negative | silent exe ":w\|bd"
endfunction

" Execute macro over visual range
xnoremap @ :<C-u>call ExecuteMacroOverVisualRange()<CR>
function! ExecuteMacroOverVisualRange() abort
  echo "@".getcmdline()
  execute ":'<,'>normal @".nr2char(getchar())
endfunction


" Execute global commands with confirmation
command! -nargs=+ -complete=command Confirm execute <SID>confirm(<q-args>) | match none
function! s:confirm(cmd) abort
  let abort = 'match none | throw "Confirm: Abort"'
  let options = [abort, a:cmd, '', abort]
  match none
  execute 'match IncSearch /\c\%' . line('.') . 'l' . @/ . '/'
  redraw
  return get(options, confirm('Execute?', "&yes\n&no\n&abort", 2), abort)
endfunction
" Run with :g/[pattern]/Confirm [command]


" Remove all text except what matches the current search result. Will put each match on its own line. This is the opposite of :%s///g (which clears all instances of the current search).
" https://github.com/idbrii/vim-searchsavvy/blob/master/autoload/searchsavvy.vim
function! ClearAllButMatches() range
    let is_whole_file = a:firstline == 1 && a:lastline == line('$')
    let old_c = @c
    let @c=""
    exec a:firstline .','. a:lastline .'sub//\=setreg("C", submatch(0), "l")/g'
    exec a:firstline .','. a:lastline .'delete _'
    put! c
    " I actually want the above to replace the whole selection with c, but I'll settle for removing the blank line that's left when deleting the file contents.
    if is_whole_file
        $delete _
    endif
    let @c = old_c
endfunction


" Remove diacritical signs from characters in specified range of lines.
" " Uses substitute so changes can be confirmed.
function! s:RemoveDiacritics(line1, line2) abort
    let diacs = 'áâãàăâçéêíîóôõșşüúţț'  " lowercase diacritical signs
    let repls = 'aaaaaaceeiiooossuutt'  " corresponding replacements
    let diacs .= toupper(diacs)
    let repls .= toupper(repls)
    let diaclist = split(diacs, '\zs')
    let repllist = split(repls, '\zs')
    let trans = {}
    for i in range(len(diaclist))
        let trans[diaclist[i]] = repllist[i]
    endfor
    execute a:line1.','.a:line2 .  's/['.diacs.']/\=trans[submatch(0)]/gIce'
endfunction
command! -range=% RemoveDiacritics call s:RemoveDiacritics(<line1>,<line2>)


" Gutentags set project roots
let g:gutentags_enabled_dirs = ['~/www']
let g:gutentags_init_user_func = 'CheckEnabledDirs'
function! CheckEnabledDirs(file) abort
    let file_path = fnamemodify(a:file, ':p:h')
    try
        let gutentags_root = gutentags#get_project_root(file_path)
        if filereadable(gutentags_root . '/_withtags')
            return 1
        endif
    catch
    endtry
    for enabled_dir in g:gutentags_enabled_dirs
        let enabled_path = fnamemodify(enabled_dir, ':p:h')
        if match(file_path, enabled_path) == 0
            return 1
        endif
    endfor
    return 0
endfunction


" Capture ex command output
function! Output(cmd) abort
  redir => message
  silent execute a:cmd
  redir END
  if empty(message)
    echoerr "no output"
  else
    tabnew
    setlocal buftype=nofile bufhidden=wipe noswapfile nobuflisted nomodified
    silent put=message
  endif
endfunction
command! -nargs=+ -complete=command Output call Output(<q-args>)
" use as :Output ex-command


" Check for file modifications automatically (current buffer only).
" Use :NoAutoChecktime to disable it (uses b:autochecktime)
fun! MyAutoCheckTime() abort
  " only check timestamp for normal files
  if &buftype != '' | return | endif
  if ! exists('b:autochecktime') || b:autochecktime
    checktime %
    let b:autochecktime = 1
  endif
endfun
augroup MyAutoChecktime
  au!
  au FocusGained,BufEnter,CursorHold * call MyAutoCheckTime()
augroup END
command! NoAutoChecktime let b:autochecktime=0


" Copy all matches
function! CopyMatches(reg)
  let hits = []
  %s//\=len(add(hits, submatch(0))) ? submatch(0) : ''/gne
  let reg = empty(a:reg) ? '+' : a:reg
  execute 'let @'.reg.' = join(hits, "\n") . "\n"'
endfunction
command! -register CopyMatches call CopyMatches(<q-reg>)


" Diff with saved file
function! s:DiffWithSaved() abort
  let filetype=&ft
  diffthis
  vnew | r # | normal! 1Gdd
  diffthis
  exe "setlocal bt=nofile bh=wipe nobl noswf ro ft=" . filetype
  normal 
endfunction
com! DiffSaved call s:DiffWithSaved()


" Set working directory to project root
function! SetProject() abort
    let path = '/mnt/c/xampp/htdocs/'
    let projects = []
    let ignore = ['_builder', 'dashboard', 'fpdf', 'git', 'img', 'php', 'php_directories', 'php_gallery', 'php_tutorial', 'sigs', 'webalizer', 'xampp']
    let dir = globpath(path, '*', 0, 1)
    call filter(dir, 'isdirectory(v:val)')
    for i in dir
        if (index(ignore, fnamemodify(i, ':p:h:t'))<0)
            call add(projects, fnamemodify(i, ':p:h:t'))
        endif
    endfor
    let options = ['']
    for i in projects
        let n = index(projects, i)+1
        let option = ' '.(n<10 ? ' '.n : n).'. '.i
        call add(options, option)
    endfor
    call inputsave()
    call add(options, '')
    let opt = inputlist(options)
    call inputrestore()
    if opt>0 && opt<=len(projects)
        exe ":tcd ".path.projects[opt-1]
        normal 
        echon "Working directory set to: "
        echohl MoreMsg | echon projects[opt-1] | echohl None
    else
        normal 
        echohl MoreMsg | echom "Nothing changed" | echohl None
    endif
endfunction
