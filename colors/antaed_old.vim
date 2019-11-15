" Structure: Material Theme by Mattia Astorino (https://github.com/equinusocio/material-theme)

" Color definitions
let s:black = "000000" " #000000
let s:dark1 = "101317" " #101317
let s:dark2 = "191E24" " #191E24
let s:dark3 = "34404D" " #34404D
let s:neutr = "51657B" " #51657B
let s:lite3 = "6E88A2" " #6E88A2
let s:lite2 = "98ACBF" " #98ACBF
let s:lite1 = "C0CDD8" " #C0CDD8
let s:white = "FFFFFF" " #FFFFFF
let s:fluor = "A5C823" " #A5C823
let s:turqo = "14ADB8" " #14ADB8
let s:purpl = "8F76E7" " #8F76E7
let s:magen = "DC1850" " #DC1850
let s:blue1 = "0F3357" " #0F3357
let s:blue2 = "112132" " #112132

" Theme setup
hi clear
syntax reset
let g:colors_name = "antaed"

" Highlighting function
fun <sid>hi(group, guifg, guibg, attr)
  if a:guifg != ""
    exec "hi " . a:group . " guifg=#" . s:gui(a:guifg)
  endif
  if a:guibg != ""
    exec "hi " . a:group . " guibg=#" . s:gui(a:guibg)
  endif
  if a:attr != ""
    exec "hi " . a:group . " gui="    . a:attr
  endif
endfun

fun s:gui(color)
  return a:color
endfun

" Vim editor colors
call <sid>hi("Bold"         , ""      , ""      , "NONE")
call <sid>hi("Italic"       , ""      , ""      , "NONE")
call <sid>hi("CursorLine"   , s:white , s:dark2 , "NONE")
call <sid>hi("Visual"       , ""      , s:dark2 , "NONE")
call <sid>hi("VisualNOS"    , ""      , s:dark2 , "NONE")
call <sid>hi("Cursor"       , ""      , s:white , "NONE")
call <sid>hi("VertSplit"    , s:black , s:black , "NONE")
call <sid>hi("MatchParen"   , s:dark1 , s:lite1 , "NONE")
call <sid>hi("IncSearch"    , s:dark1 , s:white , "NONE")
call <sid>hi("LineNr"       , s:dark3 , s:dark1 , "NONE")
call <sid>hi("TabLine"      , s:black , s:lite1 , "NONE")
call <sid>hi("Folded"       , s:neutr , s:dark1 , "NONE")
call <sid>hi("NonText"      , s:lite3 , ""      , "NONE")
call <sid>hi("SpecialKey"   , s:lite3 , ""      , "NONE")
call <sid>hi("Ignore"       , s:lite3 , s:dark1 , "NONE")
call <sid>hi("FoldColumn"   , s:neutr , s:dark1 , "NONE")
call <sid>hi("CursorLineNr" , s:lite1 , s:dark1 , "NONE")
call <sid>hi("SignColumn"   , s:white , s:dark1 , "NONE")
call <sid>hi("Search"       , s:white , s:dark3 , "NONE")
call <sid>hi("QuickFixLine" , s:white , s:dark2 , "NONE")
call <sid>hi("TabLineFill"  , s:black , s:lite1 , "NONE")
call <sid>hi("StatusLine"   , s:neutr , s:black , "NONE")
call <sid>hi("StatusLineTerm"   , s:neutr , s:black , "NONE")
call <sid>hi("StatusLineNC" , s:dark3 , s:black , "NONE")
call <sid>hi("StatusLineTermNC" , s:dark3 , s:black , "NONE")
call <sid>hi("Underlined"   , s:white , ""      , "NONE")
call <sid>hi("Question"     , s:white , ""      , "NONE")
call <sid>hi("TooLong"      , s:white , ""      , "NONE")
call <sid>hi("Error"        , s:white , s:dark1 , "NONE")
call <sid>hi("ErrorMsg"     , s:white , s:dark1 , "NONE")
call <sid>hi("WarningMsg"   , s:white , ""      , "NONE")
call <sid>hi("Debug"        , s:white , ""      , "NONE")
call <sid>hi("Macro"        , s:lite2 , ""      , "NONE")
call <sid>hi("TabLineSel"   , s:white , s:dark2 , "NONE")
call <sid>hi("Directory"    , s:lite3 , ""      , "NONE")
call <sid>hi("ModeMsg"      , s:dark1 , ""      , "NONE")
call <sid>hi("MoreMsg"      , s:white , ""      , "NONE")
call <sid>hi("Conceal"      , s:white , s:dark1 , "NONE")
call <sid>hi("WildMenu"     , s:white , s:black , "NONE")
call <sid>hi("PMenu"        , s:lite2 , s:dark2 , "NONE")
call <sid>hi("PMenuSbar"    , ""      , s:dark2 , "NONE")
call <sid>hi("PMenuThumb"   , ""      , s:dark3 , "NONE")
call <sid>hi("PMenuSel"     , s:white , s:dark2 , "NONE")
call <sid>hi("EndOfBuffer"  , s:dark1 , ""      , "NONE")

" Standard syntax highlighting
call <sid>hi("Comment"      , s:dark3 , ""      , "NONE")
call <sid>hi("Delimiter"    , s:neutr , ""      , "NONE")
call <sid>hi("Exception"    , s:neutr , ""      , "NONE")
call <sid>hi("Noise"        , s:neutr , ""      , "NONE")
call <sid>hi("StorageClass" , s:neutr , ""      , "NONE")
call <sid>hi("Keyword"      , s:lite3 , ""      , "NONE")
call <sid>hi("Function"     , s:lite3 , ""      , "NONE")
call <sid>hi("Include"      , s:lite3 , ""      , "NONE")
call <sid>hi("Type"         , s:lite3 , ""      , "NONE")
call <sid>hi("Repeat"       , s:lite3 , ""      , "NONE")
call <sid>hi("Statement"    , s:lite3 , ""      , "NONE")
call <sid>hi("Operator"     , s:lite3 , ""      , "NONE")
call <sid>hi("Conditional"  , s:lite3 , ""      , "NONE")
call <sid>hi("Label"        , s:lite3 , ""      , "NONE")
call <sid>hi("Structure"    , s:lite3 , ""      , "NONE")
call <sid>hi("Character"    , s:lite3 , ""      , "NONE")
call <sid>hi("Special"      , s:lite3 , ""      , "NONE")
call <sid>hi("SpecialChar"  , s:lite3 , ""      , "NONE")
call <sid>hi("Define"       , s:lite3 , ""      , "NONE")
call <sid>hi("Tag"          , s:lite3 , ""      , "NONE")
call <sid>hi("Typedef"      , s:lite3 , ""      , "NONE")
call <sid>hi("Todo"         , s:lite3 , s:dark1 , "NONE")
call <sid>hi("String"       , s:lite1 , ""      , "NONE")
call <sid>hi("Title"        , s:lite1 , ""      , "NONE")
call <sid>hi("Identifier"   , s:lite1 , ""      , "NONE")
call <sid>hi("Number"       , s:lite1 , ""      , "NONE")
call <sid>hi("PreProc"      , s:lite1 , ""      , "NONE")
call <sid>hi("Constant"     , s:lite1 , ""      , "NONE")
call <sid>hi("Boolean"      , s:lite1 , ""      , "NONE")
call <sid>hi("Float"        , s:lite1 , ""      , "NONE")
call <sid>hi("Normal"       , s:lite1 , s:dark1 , "NONE")
call <sid>hi("qfFilename"   , s:lite1 , ""      , "NONE")
call <sid>hi("qfSeparator"  , s:lite1 , ""      , "NONE")
call <sid>hi("qfLineNr"     , s:lite1 , ""      , "NONE")

" HTML highlighting
call <sid>hi("htmlTag"            , s:neutr , "" , "NONE")
call <sid>hi("htmlEndTag"         , s:neutr , "" , "NONE")
call <sid>hi("htmlArg"            , s:neutr , "" , "NONE")
call <sid>hi("htmlTagName"        , s:lite3 , "" , "NONE")
call <sid>hi("htmlSpecialTagName" , s:lite3 , "" , "NONE")
call <sid>hi("htmlEventDQ"        , s:lite2 , "" , "NONE")
call <sid>hi("htmlEventSQ"        , s:lite2 , "" , "NONE")
call <sid>hi("htmlLink"           , s:lite1 , "" , "NONE")
call <sid>hi("htmlBold"           , s:lite1 , "" , "NONE")
call <sid>hi("htmlItalic"         , s:lite1 , "" , "NONE")
call <sid>hi("htmlLink"           , s:lite1 , "" , "NONE")
call <sid>hi("htmlSpecialChar"    , s:lite1 , "" , "NONE")
call <sid>hi("htmlEvent"          , s:fluor , "" , "NONE")
call <sid>hi("htmlString"         , s:turqo , "" , "NONE")

" CSS highlighting
call <sid>hi("cssVendor"            , s:neutr , "" , "NONE")
call <sid>hi("cssAttrComma"         , s:neutr , "" , "NONE")
call <sid>hi("cssFunctionComma"     , s:neutr , "" , "NONE")
call <sid>hi("cssAttributeSelector" , s:lite3 , "" , "NONE")
call <sid>hi("cssDefinition"        , s:lite3 , "" , "NONE")
call <sid>hi("cssProp"              , s:lite3 , "" , "NONE")
call <sid>hi("cssKeyFrameProp"      , s:lite3 , "" , "NONE")
call <sid>hi("cssAtKeyword"         , s:lite3 , "" , "NONE")
call <sid>hi("cssBraces"            , s:lite2 , "" , "NONE")
call <sid>hi("cssAttrRegion"        , s:lite2 , "" , "NONE")
call <sid>hi("cssUnitDecorators"    , s:lite2 , "" , "NONE")
call <sid>hi("cssFunction"          , s:lite2 , "" , "NONE")
call <sid>hi("cssColor"             , s:lite2 , "" , "NONE")
call <sid>hi("cssIncludeKeyword"    , s:lite2 , "" , "NONE")
call <sid>hi("cssMediaType"         , s:lite2 , "" , "NONE")
call <sid>hi("cssFontDescriptor"    , s:lite2 , "" , "NONE")
call <sid>hi("cssPseudoClassId"     , s:lite2 , "" , "NONE")
call <sid>hi("cssSelectorOp"        , s:lite2 , "" , "NONE")
call <sid>hi("cssSelectorOp2"       , s:lite2 , "" , "NONE")
call <sid>hi("cssFunctionName"      , s:lite2 , "" , "NONE")
call <sid>hi("cssInclude"           , s:lite2 , "" , "NONE")
call <sid>hi("cssImportant"         , s:lite2 , "" , "NONE")
call <sid>hi("cssValueNumber"       , s:lite1 , "" , "NONE")
call <sid>hi("cssAttr"              , s:lite1 , "" , "NONE")
call <sid>hi("cssValueLength"       , s:lite1 , "" , "NONE")
call <sid>hi("cssValueAngle"        , s:lite1 , "" , "NONE")
call <sid>hi("cssValueTime"         , s:lite1 , "" , "NONE")
call <sid>hi("cssCommonAttr"        , s:lite1 , "" , "NONE")
call <sid>hi("cssTransitionAttr"    , s:lite1 , "" , "NONE")
call <sid>hi("cssAnimationAttr"     , s:lite1 , "" , "NONE")
call <sid>hi("cssKeyFrame"          , s:lite1 , "" , "NONE")
call <sid>hi("cssUnicodeEscape"     , s:lite1 , "" , "NONE")
call <sid>hi("cssIdentifier"        , s:magen , "" , "NONE")
call <sid>hi("cssTagName"           , s:magen , "" , "NONE")
call <sid>hi("cssClassName"         , s:magen , "" , "NONE")
call <sid>hi("cssClassNameDot"      , s:magen , "" , "NONE")
call <sid>hi("cssKeyFrameSelector"  , s:magen , "" , "NONE")

" PHP highlighting
call <sid>hi("phpRegion"          , s:neutr , "" , "NONE")
call <sid>hi("phpParent"          , s:lite2 , "" , "NONE")
call <sid>hi("phpIdentifier"      , s:purpl , "" , "NONE")
call <sid>hi("phpVarSelector"     , s:purpl , "" , "NONE")
call <sid>hi("phpIntVar"          , s:purpl , "" , "NONE")
call <sid>hi("phpMethods"         , s:purpl , "" , "NONE")
call <sid>hi("phpMethodsVar"      , s:purpl , "" , "NONE")

" JavaScript better highlighting
call <sid>hi("jsFuncArgCommas"     , s:neutr , "" , "NONE")
call <sid>hi("jsObjectProp"        , s:lite3 , "" , "NONE")
call <sid>hi("jsStorageClass"      , s:lite3 , "" , "NONE")
call <sid>hi("jsParens"            , s:lite2 , "" , "NONE")
call <sid>hi("jsFuncBraces"        , s:lite2 , "" , "NONE")
call <sid>hi("jsIfElseBraces"      , s:lite2 , "" , "NONE")
call <sid>hi("jsRepeatBraces"      , s:lite2 , "" , "NONE")
call <sid>hi("jsObjectBraces"      , s:lite2 , "" , "NONE")
call <sid>hi("jsSwitchBraces"      , s:lite2 , "" , "NONE")
call <sid>hi("jsTryCatchBraces"    , s:lite2 , "" , "NONE")
call <sid>hi("jsBrackets"          , s:lite2 , "" , "NONE")
call <sid>hi("jsThis"              , s:lite2 , "" , "NONE")
call <sid>hi("jsFunction"          , s:lite2 , "" , "NONE")
call <sid>hi("jsFuncParens"        , s:lite2 , "" , "NONE")
call <sid>hi("jsGlobalObjects"     , s:lite2 , "" , "NONE")
call <sid>hi("javaScript"          , s:lite1 , "" , "NONE")
call <sid>hi("jsFuncCall"          , s:lite1 , "" , "NONE")
call <sid>hi("jsNumber"            , s:lite1 , "" , "NONE")
call <sid>hi("jsBooleanTrue"       , s:lite1 , "" , "NONE")
call <sid>hi("jsBooleanFalse"      , s:lite1 , "" , "NONE")
call <sid>hi("jsBuiltins"          , s:lite1 , "" , "NONE")
call <sid>hi("jsUndefined"         , s:lite1 , "" , "NONE")
call <sid>hi("jsFloat"             , s:lite1 , "" , "NONE")
call <sid>hi("jsNull"              , s:lite1 , "" , "NONE")
call <sid>hi("jsException"         , s:lite1 , "" , "NONE")
call <sid>hi("jsParenIfElse"       , s:fluor , "" , "NONE")
call <sid>hi("jsIfElseBlock"       , s:fluor , "" , "NONE")
call <sid>hi("jsTernaryIf"         , s:fluor , "" , "NONE")
call <sid>hi("jsSwitchBlock"       , s:fluor , "" , "NONE")
call <sid>hi("jsRepeatBlock"       , s:fluor , "" , "NONE")
call <sid>hi("jsFuncBlock"         , s:fluor , "" , "NONE")
call <sid>hi("jsObjectKey"         , s:fluor , "" , "NONE")
call <sid>hi("jsParen"             , s:fluor , "" , "NONE")
call <sid>hi("jsParenRepeat"       , s:fluor , "" , "NONE")
call <sid>hi("jsGlobalNodeObjects" , s:fluor , "" , "NONE")
call <sid>hi("jsObjectValue"       , s:fluor , "" , "NONE")
call <sid>hi("jsVariableDef"       , s:fluor , "" , "NONE")
call <sid>hi("jsFuncName"          , s:fluor , "" , "NONE")
call <sid>hi("jsFunctionKey"       , s:fluor , "" , "NONE")
call <sid>hi("jsFuncArgs"          , s:fluor , "" , "NONE")
call <sid>hi("jsBracket"           , s:fluor , "" , "NONE")
call <sid>hi("jsPrototype"         , s:fluor , "" , "NONE")
call <sid>hi("jsExceptions"        , s:fluor , "" , "NONE")
call <sid>hi("jsArguments"         , s:fluor , "" , "NONE")
call <sid>hi("jsAsyncKeyword"      , s:fluor , "" , "NONE")
call <sid>hi("jsParenSwitch"       , s:fluor , "" , "NONE")

" Default JavaScript
call <sid>hi("javaScriptStringS"    , s:lite3 , "" , "NONE")
call <sid>hi("javaScriptIdentifier" , s:lite3 , "" , "NONE")
call <sid>hi("javaScriptParens"     , s:lite3 , "" , "NONE")

" VimScript
call <sid>hi("vimNotation"  , s:lite2 , "" , "NONE")
call <sid>hi("vimMapModKey" , s:lite2 , "" , "NONE")

" Diff highlighting
call <sid>hi("DiffAdd"     , s:white , s:blue1 , "NONE")
call <sid>hi("DiffChange"  , ""      , s:blue2 , "NONE")
call <sid>hi("DiffDelete"  , s:black , s:black , "NONE")
call <sid>hi("DiffText"    , s:white , s:blue1 , "NONE")

" Git highlighting
call <sid>hi("gitCommitOverflow" , s:magen , "" , "NONE")
call <sid>hi("gitCommitSummary"  , s:lite3 , "" , "NONE")

" GitGutter highlighting
call <sid>hi("GitGutterAdd"          , s:turqo , s:dark2 , "NONE")
call <sid>hi("GitGutterChange"       , s:fluor , s:dark2 , "NONE")
call <sid>hi("GitGutterDelete"       , s:magen , s:dark2 , "NONE")
call <sid>hi("GitGutterChangeDelete" , s:lite2 , s:dark2 , "NONE")

" Markdown highlighting
call <sid>hi("markdownCode"             , s:turqo , ""      , "NONE")
call <sid>hi("markdownError"            , s:magen , s:dark1 , "NONE")
call <sid>hi("markdownCodeBlock"        , s:turqo , ""      , "NONE")
call <sid>hi("markdownHeadingDelimiter" , s:lite2 , ""      , "NONE")

" SASS highlighting
call <sid>hi("sassidChar"                 , s:magen , "" , "NONE")
call <sid>hi("sassClassChar"              , s:magen , "" , "NONE")
call <sid>hi("sassInclude"                , s:lite2 , "" , "NONE")
call <sid>hi("sassMixing"                 , s:lite2 , "" , "NONE")
call <sid>hi("sassMixinName"              , s:lite3 , "" , "NONE")
call <sid>hi("sassVariable"               , s:magen , "" , "NONE")
call <sid>hi("sassClass"                  , s:magen , "" , "NONE")
call <sid>hi("sassProperty"               , s:lite3 , "" , "NONE")
call <sid>hi("sassDefinition"             , s:lite3 , "" , "NONE")
call <sid>hi("sassCssAttribute"           , s:lite2 , "" , "NONE")
call <sid>hi("sassInterpolationDelimiter" , s:lite2 , "" , "NONE")
call <sid>hi("sassVariableAssignment"     , s:lite3 , "" , "NONE")

" Spelling highlighting
call <sid>hi("SpellBad"   , "" , s:dark1 , "undercurl")
call <sid>hi("SpellLocal" , "" , s:dark1 , "undercurl")
call <sid>hi("SpellCap"   , "" , s:dark1 , "undercurl")
call <sid>hi("SpellRare"  , "" , s:dark1 , "undercurl")

" CtrlP
" For the CtrlP buffer:
call <sid>hi("CtrlPBufferNr"    , s:lite1 , ""      , "NONE")
call <sid>hi("CtrlPLinePre"     , s:dark1 , s:dark1 , "NONE") " the line prefix '>' in the match window
call <sid>hi("CtrlPMatch"       , s:white , ""      , "NONE")
" Highlight groups:
call <sid>hi("CtrlPMode2"   , s:neutr , s:black , "NONE") " 'prt' or 'win' , 'regex' , the working directory (|hl-LineNr|)
call <sid>hi("CtrlPMode1"   , s:lite3 , s:black , "NONE") " 'file' or 'path' or 'line' , and the current mode (Character)

" Remove functions
delf <sid>hi
delf <sid>gui

" Remove color variables
unlet s:black s:dark1 s:dark2 s:dark3 s:neutr s:lite3 s:lite2 s:lite1 s:white s:fluor s:turqo s:purpl s:magen
