(function() {
  const collapsibles = document.querySelectorAll( '.js-details' );
  Array.prototype.forEach.call(collapsibles, function(collapsible)
  {
    collapsible.dataset['expanded'] = false;
    let btn = collapsible.querySelector( 'button' );
    btn.onclick = function()
    {
      let expanded = btn.getAttribute( 'aria-expanded' ) === 'true';
      btn.setAttribute( 'aria-expanded', !expanded );
      collapsible.setAttribute( 'data-expanded', !expanded );
    }
  });
})()