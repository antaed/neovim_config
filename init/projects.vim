" Projects
let g:projects_path = '/mnt/c/xampp/htdocs/'

" Auto tcd on tabnew
function! OnTabEnter(path)
  if isdirectory(a:path)
    let dirname = a:path
  else
    let dirname = fnamemodify(a:path, ":h")
  endif
  execute "tcd ". dirname
endfunction()
autocmd TabNewEntered * call OnTabEnter(expand("<amatch>"))

" cd with fzf
command! -nargs=* -complete=dir Cd call fzf#run(fzf#wrap({'source': 'find '.g:projects_path.<f-args>.' -maxdepth 1 -type d -print 2> /dev/null', 'sink': function('<sid>cdir'), 'options': '--prompt "Projects> "'}))
function! s:cdir(args)
    execute "tcd ".a:args
    execute "Files"
    execute feedkeys('i','n')
endfunction

" FTP
let g:sites = [
    \'adrya.ro',
    \'apffp.ro',
    \'artvinium.ro',
    \'autoelite.ro',
    \'avocat-tehei.eu',
    \'cbms.ro',
    \'cheltuielidebloc.ro',
    \'concise.ro',
    \'delta-net.ro',
    \'dieselevents.ro',
    \'ebuild.crhromania.ro',
    \'galiceamare.com',
    \'ginco.ro',
    \'granatagym.ro',
    \'ingridvlasovshop.com',
    \'ingridvlasovstyle.com',
    \'instalatiiandapaula.ro',
    \'iuliacirstea.com',
    \'modern-rustic.ro',
    \'navetesilazi.ro',
    \'one-way.ro',
    \'opticris.ro',
    \'pensiuneadaciiliberi.ro',
    \'popacademy.ro',
    \'power-zone.ro',
    \'pr0-fit.ro',
    \'rinman.ro',
    \'rpdcurier.ro',
    \'rsc-consulting.ro',
    \'salesconsulting.ro',
    \'scoala-stewardese.ro',
    \'setting.ro',
    \'sis-centrum.ro',
    \'taqtmedia.ro',
    \'tdstudio.ro',
    \'universifly.com',
    \'vivendis.ro',
    \'woodcore.ro',
    \'zozorent.ro'
    \]
command! -nargs=* -complete=dir Ftp call fzf#run(fzf#wrap({'source': g:sites, 'sink': function('<sid>ftpopen'), 'options': '--prompt "Sites> "'}))
function! s:ftpopen(args)
    execute "e ftp://".a:args."/"
endfunction
