let s:p = { 'normal': {}, 'command': {}, 'inactive': {}, 'insert': {}, 'replace': {}, 'terminal': {}, 'visual': {}, 'tabline': {} }

let s:black = [ '#000000', '0'   ] " #000000
let s:dark1 = [ '#0E101B', '232' ] " #0E101B
let s:dark2 = [ '#161B2C', '235' ] " #161B2C
let s:dark3 = [ '#2E3A5C', '237' ] " #2E3A5C
let s:neutr = [ '#495F92', '241' ] " #495F92
let s:lite3 = [ '#6982B4', '244' ] " #6982B4
let s:lite2 = [ '#96A9CA', '248' ] " #96A9CA
let s:lite1 = [ '#BFCBDF', '252' ] " #BFCBDF
let s:white = [ '#FFFFFF', '15'  ] " #FFFFFF
let s:fluor = [ '#A3C924', '148' ] " #A3C924
let s:turqo = [ '#00BAC7', '44'  ] " #00BAC7
let s:purpl = [ '#9E6CEA', '98'  ] " #9E6CEA
let s:magen = [ '#DC184D', '197' ] " #DC184D
let s:indig = [ '#6C76D5', '197' ] " #6C76D5

let s:p.normal.left     = [ [ s:black , s:fluor ] , [ s:fluor , s:black ] , [ s:neutr , s:black ] , [ s:neutr , s:black ] ]
let s:p.command.left    = [ [ s:black , s:turqo ] , [ s:turqo , s:black ] , [ s:neutr , s:black ] , [ s:neutr , s:black ] ]
let s:p.insert.left     = [ [ s:white , s:magen ] , [ s:magen , s:black ] , [ s:neutr , s:black ] , [ s:neutr , s:black ] ]
let s:p.visual.left     = [ [ s:black , s:purpl ] , [ s:purpl , s:black ] , [ s:neutr , s:black ] , [ s:neutr , s:black ] ]
let s:p.replace.left    = [ [ s:black , s:turqo ] , [ s:turqo , s:black ] , [ s:neutr , s:black ] , [ s:neutr , s:black ] ]
let s:p.terminal.left   = [ [ s:black , s:turqo ] , [ s:turqo , s:black ] , [ s:neutr , s:black ] , [ s:neutr , s:black ] ]
let s:p.inactive.left   = [ [ s:neutr , s:dark2 ] , [ s:lite3 , s:black ] , [ s:neutr , s:black ] ]
let s:p.normal.right    = [ [ s:fluor , s:black ] , [ s:fluor , s:black ] ]
let s:p.command.right   = [ [ s:turqo , s:black ] , [ s:turqo , s:black ] ]
let s:p.insert.right    = [ [ s:magen , s:black ] , [ s:magen , s:black ] ]
let s:p.visual.right    = [ [ s:purpl , s:black ] , [ s:purpl , s:black ] ]
let s:p.replace.right   = [ [ s:turqo , s:black ] , [ s:turqo , s:black ] ]
let s:p.terminal.right  = [ [ s:turqo , s:black ] , [ s:turqo , s:black ] ]
let s:p.inactive.right  = [ [ s:lite3 , s:black ] , [ s:lite3 , s:black ] ]
let s:p.normal.middle   = [ [ s:neutr , s:black ] ]
let s:p.command.middle  = [ [ s:neutr , s:black ] ]
let s:p.inactive.middle = [ [ s:neutr , s:black ] ]
let s:p.tabline.left    = [ [ s:neutr , s:black ] , [ s:neutr , s:black ] , [ s:neutr , s:black ] ]
let s:p.tabline.tabsel  = [ [ s:white , s:black ] ]
let s:p.tabline.middle  = [ [ s:neutr , s:black ] ]
let s:p.tabline.right   = [ [ s:black , s:black ] ]
let s:p.normal.error    = [ [ s:magen , s:black ] ]
let s:p.normal.warning  = [ [ s:fluor , s:black ] ]
let s:p.normal.hints     = [ [ s:turqo , s:black ] ]
let s:p.normal.information = [ [ s:indig , s:black ] ]
let s:p.command.error   = [ [ s:magen , s:black ] ]
let s:p.command.warning = [ [ s:fluor , s:black ] ]

let g:lightline#colorscheme#antaed#palette = lightline#colorscheme#flatten(s:p)
