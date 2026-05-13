// https://kamranahmed.info/toast
function jtoast(c, p = 'bottom-center') {
    var myToast = $.toast({
        text: c,
        showHideTransition: 'fade',
        position: p,
        loader: false,

    })
}
