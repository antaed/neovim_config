syn clear htmlCommentPart
syn clear htmlCommentError
syn match htmlCommentInside "<!--\zs\_.\{-}\ze-->" contained containedin=htmlComment contains=jsphp
syn match jsjq "\$\(.\|(\)\@<=" contained
syn match jsdt "\$dt\(.\|(\)\@<=" contained
syn match jsprop "\.\zs\h\w*\ze" contained
syn match jsword "\h\w*" contains=jsKwd,jsObj,jsMtd,jsPrp,jsFxMtd,jsFxPrp contained
syn match jspct "[,;]" contained
syn match jsop "[-=+%^&|*!.~?:]" contained
syn match jsop "[-+*/%^&|.!=<>]=" contained
syn match jsop "&&\|\<and\>" contained
syn match jsop "||\|\<x\=or\>" contained
syn match jsop "[<>]" contained
syn match phpFunc "\h\w*\ze(" contained containedin=phpRegion
syn match phpDef "\h\u*_*\u\+" contained containedin=phpRegion
syn match phpCap "\u\w\+" contained containedin=phpRegion
syn keyword phpGlobals $GLOBALS $_SERVER $_REQUEST $_POST $_GET $_FILES $_ENV $_COOKIE $_SESSION contained containedin=phpRegion
syn keyword phpFunctions unixtojd timestampFromString contained
syn region jsphp start=+<?\(php\|=\)\?+ end=+?>+ keepend contained contains=@htmlPreproc
syn cluster htmlJavaScript add=jsprop,jsword,jspct,jsop,jsphp,jsjq,jsdt
syn region javaScript start=+<script\_[^>]*>+ keepend end=+</script\_[^>]*>+me=s-1 contains=@htmlJavaScript,htmlCssStyleComment,htmlScriptTag,@htmlPreproc,jsphp
syn region htmlScriptTag contained start=+<script+ end=+>+ fold contains=htmlTagN,htmlString,htmlArg,htmlValue,htmlTagError,htmlEvent
hi def link htmlScriptTag htmlTag
syn match htmlXTagName "x-\a\+\(\(-\|::\|\.\)\?\a\+\)*" contained containedin=htmlTagN
syn region htmlXTag contained start=+<x-\a\++ end=+>+me=s-1 contains=htmlTagN,htmlString,htmlArg,htmlValue,htmlTagError,htmlEvent,htmlXTagAttr,htmlXTagAlpine containedin=htmlTag
hi def link htmlXTag htmlTag
hi def link htmlXTagName htmlTagName
syn region htmlXTagAttr contained start=+\(<\|\s\+\)\zs:\a\+\s*=[\t ]*"+ end=+"+ keepend contains=htmlXTagValue
syn region htmlXTagAlpine contained start=+\(<\|\s\+\)\zs::\a\+\s*=[\t ]*"+ end=+"+ keepend contains=htmlEventDQ
syn region htmlXTagValue contained start=+"+ms=s+1 end=+"+me=s-1 contains=@phpClTop
syn region htmlAlpine contained start=+\(<\|\s\+\)\zs\(x-\|@\|:\)\a\+\([.]\?\a\+\)*\s*=[\t ]*"+ end=+"+ contains=htmlEventDQ containedin=htmlTag
hi def link htmlAlpine htmlTag
hi def link htmlXTagAttr htmlTag
hi def link htmlXTagAlpine htmlTag
hi def link htmlCommentInside comment
