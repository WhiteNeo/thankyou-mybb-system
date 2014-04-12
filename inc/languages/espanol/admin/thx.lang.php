<?php
/**
 * Autor: Dark Neo
 * Plugin: Sistema de Agradecimientos
 * Versión: 2.3.2
 * Archivo de Idioma del Plugin: espanol
 */

$l['thx_title'] = "Sistema de agradecimientos";
$l['thx_desc'] = "Agrega un botón para dar gracias en los foros que aplica, a los temas de usuarios";
$l['thx_config'] = "Configurar Plugin";
$l['thx_disabled'] = "Plugin Desactivado";
$l['thx_opt_title'] = "[Plugin] Sistema de Agradecimientos";
$l['thx_opt_desc'] = "Configuración del plugin";
$l['thx_opt_enable'] = "Activar/Desactivar plugin";
$l['thx_opt_enable_desc'] = "Activar o desactivar el plugin, únicamente lo deshabilita (deja intactos los agradecimientos)";
$l['thx_count_title'] = "Mostrar conteo de agradecimientos";
$l['thx_count_desc'] = "Muestra los agradecimientos dados y recibidos en el mensaje";
$l['thx_counter_title'] = "Mostrar contador de agradecimientos";
$l['thx_counter_desc'] = "Muestra el total de agradecimientos recibidos debajo de cada mensajes";
$l['thx_del_title'] = "Los usuarios pueden eliminar sus agradecimientos";
$l['thx_del_desc'] = "Cada usuario puede eliminar los agradecimientos que haya dado";
$l['thx_date_title'] = "Mostrar fecha al alejar el raton";
$l['thx_date_desc'] = "Muestra la fecha del agradecimiento, cuando el raton paso por encima del texto";
$l['thx_temp_title'] = "Auto deteccion de plantillas";
$l['thx_temp_desc'] = "Detectar plantilla del postbit relacionando el codigo HTML! (solo funciona si \"Separar tablas\" esta Habilitado)";
$l['thx_table_title'] = "Separar tablas";
$l['thx_table_desc'] = "Si quiere mostrar los agradecimientos entre los mensajes <b>(no al final del mensaje)</b>, active esta opción.";
$l['thx_hide_title'] = "Utilizar etiqueta [???]";
$l['thx_hide_desc'] = "Oculta los contenidos entre la etiqueta <b>[???]</b>, hasta agradecer al tema. <div style=\"float:right;\"><b>Utilice la opción de abajo para establecer la etiqueta para ocultar contenidos</b></div>";
$l['thx_hidetag_title'] = "Etiqueta a utilizar para ocultar contenidos en sus mensajes Ej: [???]";
$l['thx_hidetag_desc'] = "Elija el texto para la etiqueta dentro de <b>[???]</b>, para utilizar (MyCode).<div style=\"float:right;\"><b>Debe habilitar la opción de arriba para utililzar esta función.<br />Ej de etiquetas a utilizar: oculto, hide, etc...</b></div>";
$l['thx_hidetag_value'] = "oculto";
$l['thx_ebutton_title'] = "Botón en el Editor";
$l['thx_ebutton_desc'] = "Mostrar el botón en el editor para el MyCode (Necesitas habilitar la etiqueta oculto.)";
$l['thx_fid_title'] = "Foros en los que aplica el sistema de agradecimientos";
$l['thx_fid_desc'] = "Coloque la id de los foros en los que se implementara el sistema de agradecimientos, separados por coma. <div style=\"float:right;\"><b>Ej: 2,3,4. Por defecto foro principal (ID 2)</b></div>";
$l['thx_gid_title'] = "Grupos que pueden mirar los contenidos de la etiqueta [???]";
$l['thx_gid_desc'] = "Id de los grupos de usuarios que pueden ver todo el contenido separados por coma.<div style=\"float:right;\"><b>Ej: 3,4(Moderador, Administrador)</b></div>";
$l['thx_ngid_title'] = "Grupos que no pueden mirar los contenidos de la etiqueta [???]";
$l['thx_ngid_desc'] = "Id de los grupos de usuarios que no pueden ver el contenido separados por coma. <div style=\"float:right;\"><b>Ej: 1,5,7(Invitado, Cuenta por activar, Bloqueado)</b></div>";
$l['thx_rep_title'] = "Integrar este sistema a la reputación?";
$l['thx_rep_desc'] = "Habilitar o no el sistema de reputación de MyBB al agradecer a un tema, para la tercera opción forzosamete necesitas instalar MyAlerts de euantor o te dará error";
$l['thx_rep_op1'] = "Deshabilitar";
$l['thx_rep_op2'] = "Integrar reputacion";
$l['thx_rep_op3'] = "Integrar reputacion + MyAlerts";
$l['thx_recount'] = "Recontar Agradecimientos";
$l['thx_can_recount'] = "Habilitar Reconteo de Agradecimientos";
$l['thx_recount_do'] = "Reconteo de Agradecimientos";
$l['thx_upgrade_do'] = "Actualizar el conteo de los agradecimientos";
$l['thx_recount_task'] = "Tarea";
$l['thx_recount_task'] = "Envíos/Ciclo";
$l['thx_recount_update'] = "Actualizar contador de agradecimientos";
$l['thx_recount_update_desc'] = "Reconstruir información de agradecimientos dados/recibidos en sus mensajes.";
$l['thx_recount_update_button'] = "Actualizar";
$l['thx_counter_update'] = "Actualizar Contadores";
$l['thx_counter_update_desc'] = "Actualiza el conteo de agradecimientos";
$l['thx_confirm_next'] = '<p>Clic en CONTINUAR para hacer el conteo de los agradecimientos.</p><p>Si has llegado a ésta página es porque aún faltan contar agradecimentos por los envíos por ciclo elegidos</p><p>Da clic en Continuar hasta que el proceso finalice con exito.</p>';
$l['thx_confirm_button'] = "Continuar";
$l['thx_confirm_page'] = "# de página: ";
$l['thx_confirm_elements'] = "Elementos Restantes";
$l['thx_update_psuccess'] = "Se han reconstruido los agradecimientos con éxito";
$l['thx_update_tsuccess'] = "Se ha actualizado el conteo de agradecimientos con éxito";
$l['thx_thankyou'] = "Gracias por el aporte";
$l['thx_alerts_title'] = "Agregar el sistema de alertas al agradecer?";
$l['thx_alerts_title_desc'] = "Agrega una alerta al usuario cuando alguien agradezca en sus mensajes (Necesitas el Sistema de Agradecimientos)";
$l['thx_alerts_install_error'] = "Debes tener instalador el plugin MyAlerts para agregar alertas en los mensajes, si lo instalas, puedes desactivar y activar este pllugin de nuevo, esto no elimina tus agradecimientos y te permite agregar alertas al agradecer utilizando MyAlerts";
?>