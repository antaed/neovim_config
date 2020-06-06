if (has("termguicolors"))
    set termguicolors
endif

let g:colors_name_negative = g:colors_name=='antaed' ? 'antaed_light' : 'antaed'

let g:lightline = { 'colorscheme': g:colors_name, 'mode_map': { 'n': '  N', 'c': '  C', 'i': '  I', 'v':'  V', 'V': ' VL', "\<C-v>": ' VB', 'R': '  R', '?': '   ', 't': '  T' },
            \ 'active':   { 'left': [ [ 'mode' ], [ 'modified', 'readonly' ], [ 'filename' ], [ 'cocerror' ], [ 'cocwarn' ], [ 'cochint' ], [ 'cocinfo' ] ] }, 
            \ 'inactive': { 'left': [ [ 'mode' ], [ 'modified' ], [ 'filename' ] ] }, 'subseparator': { 'left': '', 'right':'' }, 
            \ 'component_function': { 'modified': 'CustomModified' },
            \ 'component_expand': {
                    \ 'cocerror': 'LightLineCocError',
                    \ 'cocwarn' : 'LightLineCocWarn',
                    \ 'cochint' : 'LightLineCocHint',
                    \ 'cocinfo' : 'LightLineCocInfo' },
            \ }
function! CustomModified()
    return &modified ? '+' : ''
endfunction

" sync colorscheme
augroup lightline-events
    autocmd!
    autocmd ColorScheme * call s:onColorSchemeChange(expand("<amatch>"))
    autocmd User CocDiagnosticChange call lightline#update()
augroup END
let s:colour_scheme_map = {g:colors_name_negative : g:colors_name}
function! s:onColorSchemeChange(scheme)
    " Try a scheme provided already
    execute 'runtime autoload/lightline/colorscheme/'.a:scheme.'.vim'
    if exists('g:lightline#colorscheme#{a:scheme}#palette')
        let g:lightline.colorscheme = a:scheme
    else  " Try falling back to a known colour scheme
        let l:colors_name = get(s:colour_scheme_map, a:scheme, '')
        if empty(l:colors_name)
            return
        else
            let g:lightline.colorscheme = l:colors_name
        endif
    endif
    call lightline#init()
    call lightline#colorscheme()
    call lightline#update()
endfunction

" custom coc-diagnose highlights
function! s:LightlineCodDiagnostics(sign, kind) abort
    let g:lightline.component_type = {
                \   'cocerror': 'error',
                \   'cocwarn' : 'warning',
                \   'cochint' : 'hints',
                \   'cocinfo' : 'info',
                \ }
  let css = { 'E:': 'coc_status_error_sign', 'W:': 'coc_status_warning_sign', 'H:': 'coc_status_hint_sign', 'I:': 'coc_status_info_sign' }
  let sign = get(g:, css[a:sign], a:sign)
  let info = get(b:, 'coc_diagnostic_info', {})
  if empty(info)
    return ''
  endif
  let msgs = []
  if get(info, a:kind, 0)
    call add(msgs, sign . info[a:kind])
  endif
  return trim(join(msgs, ' ') . ' ' . get(g:, 'coc_status', ''))
endfunction
function! LightLineCocError() abort
    return s:LightlineCodDiagnostics('E:','error')
endfunction
function! LightLineCocWarn() abort
    return s:LightlineCodDiagnostics('W:','warning')
endfunction
function! LightLineCocHint() abort
    return s:LightlineCodDiagnostics('H:','hint')
endfunction
function! LightLineCocInfo() abort
    return s:LightlineCodDiagnostics('I:','info')
endfunction

if !exists("g:syntax_on")
    syntax enable
endif

if has("gui_running")
  if has("gui_gtk2") || has("gui_gtk3")
    set guifont=Consolas\ 11
  elseif has("gui_photon")
    set guifont=Consolas:s11
  elseif has("gui_kde")
    set guifont=Consolas/11/-1/5/50/0/0/0/1/0
  elseif has("x11")
    set guifont=-*-consolas-medium-r-normal-*-*-180-*-*-m-*-*
  else
    set guifont=M+\ 1mn\ light:h12:cDEFAULT
  endif
endif

" Open help files in new tab
cnoreabbrev <expr> help ((getcmdtype() is# ':'    && getcmdline() is# 'help')?('tab help'):('help'))
cnoreabbrev <expr> h ((getcmdtype() is# ':'    && getcmdline() is# 'h')?('tab help'):('h'))
