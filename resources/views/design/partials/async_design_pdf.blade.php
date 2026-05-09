<script>
(function ($) {
  function partilotRemoveAllNotifies() {
    if (typeof PNotify !== 'undefined' && typeof PNotify.removeAll === 'function') {
      PNotify.removeAll();
    }
    document.querySelectorAll('.ui-pnotify').forEach(function (el) {
      el.remove();
    });
  }

  function partilotTriggerDownload(url) {
    var iframe = document.createElement('iframe');
    iframe.setAttribute('style', 'display:none;width:0;height:0;border:0');
    iframe.setAttribute('src', url);
    document.body.appendChild(iframe);
    setTimeout(function () {
      iframe.remove();
    }, 180000);
  }

  function partilotNotifyPdf(type, title, message, sticky) {
    if (typeof PNotify === 'undefined') {
      if (message) window.alert(title + '\\n\\n' + message);
      return;
    }
    partilotRemoveAllNotifies();
    var opts = {
      type: type,
      addclass: 'partilot-notify',
      width: '460px',
      title: title,
      text: message,
      icon: false,
      opacity: 1,
      nonblock: false,
      styling: 'bootstrap3',
      buttons: { closer: true, sticker: false, closer_hover: false }
    };
    if (sticky) {
      opts.hide = false;
    } else {
      opts.hide = true;
      opts.delay = 6000;
    }
    new PNotify(opts);
  }

  function partilotPollPdfStatus(checkUrl, notifyTitle, attemptsLeft, restoreBtn, $restoreEl) {
    if (attemptsLeft <= 0) {
      if (restoreBtn && $restoreEl && $restoreEl.length) $restoreEl.prop('disabled', false);
      partilotNotifyPdf('error', notifyTitle || 'PDF', 'El tiempo de espera para el PDF terminó sin resultado. Revise si el worker de colas está en ejecución.');
      return;
    }
    $.getJSON(checkUrl)
      .done(function (st) {
        if (st && st.status === 'completed' && st.download_url) {
          if (restoreBtn && $restoreEl && $restoreEl.length) $restoreEl.prop('disabled', false);
          partilotRemoveAllNotifies();
          partilotTriggerDownload(st.download_url);
          partilotNotifyPdf('success', notifyTitle || 'PDF', 'Descarga iniciada. Si no ve el archivo, compruebe descargas y el bloqueador.', false);
          return;
        }
        setTimeout(function () {
          partilotPollPdfStatus(checkUrl, notifyTitle, attemptsLeft - 1, restoreBtn, $restoreEl);
        }, 2000);
      })
      .fail(function () {
        if (restoreBtn && $restoreEl && $restoreEl.length) $restoreEl.prop('disabled', false);
        partilotNotifyPdf('error', notifyTitle || 'PDF', 'No se pudo consultar el estado del PDF.');
      });
  }

  $(document).on('click', '.js-design-pdf-async', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $btn = $(this);
    var url = $btn.data('async-url');
    var title = $btn.data('title') || 'PDF';
    if (!url) return;
    $btn.prop('disabled', true);
    partilotNotifyPdf('info', title, 'Generando PDF… Puede tardar varios minutos según el volumen.', true);
    $.ajax({ url: url, method: 'GET', dataType: 'json' })
      .done(function (data) {
        if (data && data.status === 'processing' && data.check_url) {
          partilotPollPdfStatus(data.check_url, title, 1800, true, $btn);
          return;
        }
        $btn.prop('disabled', false);
        partilotNotifyPdf('error', title, data && data.message ? data.message : 'Respuesta inesperada al iniciar la generación.', false);
      })
      .fail(function (xhr) {
        $btn.prop('disabled', false);
        var msg = 'No se pudo iniciar la generación del PDF.';
        try {
          var j = xhr.responseJSON;
          if (j && j.message) msg = j.message;
        } catch (err) {}
        partilotNotifyPdf('error', title, msg, false);
      });
  });
})(window.jQuery);
</script>
