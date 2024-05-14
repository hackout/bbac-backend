const Ziggy = {"url":"http:\/\/localhost:8000","port":8000,"defaults":{},"routes":{"sanctum.csrf-cookie":{"uri":"sanctum\/csrf-cookie","methods":["GET","HEAD"]},"ignition.healthCheck":{"uri":"_ignition\/health-check","methods":["GET","HEAD"]},"ignition.executeSolution":{"uri":"_ignition\/execute-solution","methods":["POST"]},"ignition.updateConfig":{"uri":"_ignition\/update-config","methods":["POST"]},"captcha":{"uri":"captcha","methods":["GET","HEAD"]},"login":{"uri":"login","methods":["POST"]},"forget":{"uri":"forget","methods":["GET","HEAD"]},"forget.send":{"uri":"forget\/send","methods":["POST"]},"forget.check":{"uri":"forget\/check","methods":["POST"]},"forget.reset":{"uri":"forget\/reset","methods":["POST"]},"login.first":{"uri":"login\/first","methods":["POST"]},"logout":{"uri":"logout","methods":["POST"]},"dashboard":{"uri":"\/","methods":["GET","HEAD"]},"check.password":{"uri":"check","methods":["POST"]},"uploadImage":{"uri":"upload\/image","methods":["POST"]},"uploadVideo":{"uri":"upload\/video","methods":["POST"]},"uploadFile":{"uri":"upload\/file","methods":["POST"]},"profile.index":{"uri":"profile","methods":["POST"]},"profile.account":{"uri":"profile\/account","methods":["POST"]},"profile.password":{"uri":"profile\/password","methods":["POST"]},"profile.log":{"uri":"profile\/log","methods":["GET","HEAD"]},"profile.unread":{"uri":"profile\/unread","methods":["GET","HEAD"]},"profile.read":{"uri":"profile\/read\/{id}","methods":["POST"],"parameters":["id"]},"profile.delete":{"uri":"profile\/delete\/{id}","methods":["DELETE"],"parameters":["id"]},"profile.approve":{"uri":"profile\/approve\/{id}","methods":["POST"],"parameters":["id"]},"profile.message":{"uri":"profile\/message","methods":["GET","HEAD"]},"profile.message_detail":{"uri":"profile\/message\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"role.index":{"uri":"role","methods":["GET","HEAD"]},"role.list":{"uri":"role\/list","methods":["GET","HEAD"]},"role.create":{"uri":"role","methods":["POST"]},"role.batch_delete":{"uri":"role\/batch","methods":["POST"]},"role.update":{"uri":"role\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"role.valid":{"uri":"role\/{id}","methods":["PATCH"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"department.index":{"uri":"department","methods":["GET","HEAD"]},"department.list":{"uri":"department\/list","methods":["GET","HEAD"]},"department.create":{"uri":"department","methods":["POST"]},"department.import":{"uri":"department\/import","methods":["POST"]},"department.template":{"uri":"department\/template","methods":["GET","HEAD"]},"department.batch_delete":{"uri":"department\/batch","methods":["POST"]},"department.update":{"uri":"department\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"dict.index":{"uri":"dict","methods":["GET","HEAD"]},"dict.list":{"uri":"dict\/list","methods":["GET","HEAD"]},"dict.create":{"uri":"dict","methods":["POST"]},"dict.option":{"uri":"dict\/{code}","methods":["GET","HEAD"],"parameters":["code"]},"dict.export":{"uri":"dict\/export","methods":["POST"]},"dict.update":{"uri":"dict\/{id}","methods":["PUT"],"wheres":{"id":"[0-9]+"},"parameters":["id"]},"dict_item.list":{"uri":"dict\/item\/{code}","methods":["GET","HEAD"],"wheres":{"code":"[0-9a-zA-Z\\-_]+"},"parameters":["code"]},"dict_item.create":{"uri":"dict\/item\/{code}","methods":["POST"],"wheres":{"code":"[0-9a-zA-Z\\-_]+"},"parameters":["code"]},"dict_item.update":{"uri":"dict\/item\/{code}\/{id}","methods":["PUT"],"wheres":{"code":"[0-9a-zA-Z\\-_]+","id":"[0-9]+"},"parameters":["code","id"]},"dict_item.batch_delete":{"uri":"dict\/item\/{code}\/delete","methods":["POST"],"wheres":{"code":"[0-9a-zA-Z\\-_]+"},"parameters":["code"]},"system_config.index":{"uri":"system","methods":["POST"]},"system_config.cache":{"uri":"system\/cache","methods":["GET","HEAD"]},"system_config.cache_clear":{"uri":"system\/cache","methods":["POST"]},"user_log.index":{"uri":"log","methods":["GET","HEAD"]},"user_log.list":{"uri":"log\/list","methods":["GET","HEAD"]},"locale_package.index":{"uri":"language","methods":["GET","HEAD"]},"locale_package.list":{"uri":"language\/list","methods":["GET","HEAD"]},"locale_package.create":{"uri":"language","methods":["POST"]},"locale_package.export":{"uri":"language\/export","methods":["POST"]},"locale_package.import":{"uri":"language\/import","methods":["POST"]},"locale_package.template":{"uri":"language\/template","methods":["GET","HEAD"]},"locale_package.update":{"uri":"language\/{id}","methods":["PUT"],"wheres":{"id":"[0-9]+"},"parameters":["id"]},"user.index":{"uri":"user","methods":["GET","HEAD"]},"user.list":{"uri":"user\/list","methods":["GET","HEAD"]},"user.department":{"uri":"user\/department\/{department_id}","methods":["GET","HEAD"],"wheres":{"department_id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["department_id"]},"user.create":{"uri":"user","methods":["POST"]},"user.export":{"uri":"user\/export","methods":["POST"]},"user.import":{"uri":"user\/import","methods":["POST"]},"user.template":{"uri":"user\/template","methods":["GET","HEAD"]},"user.batch_delete":{"uri":"user\/delete","methods":["POST"]},"user.detail":{"uri":"user\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"user.update":{"uri":"user\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"user.update_detail":{"uri":"user\/{id}","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"user.patch":{"uri":"user\/{slug}","methods":["POST"],"wheres":{"slug":"invalid|valid|unlock|lock"},"parameters":["slug"]},"user.birthday":{"uri":"user\/birthday","methods":["GET","HEAD"]},"user.birthday_list":{"uri":"user\/birthday\/list","methods":["GET","HEAD"]},"user.birthday_update":{"uri":"user\/birthday\/{id}","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"birthday_card.list":{"uri":"birthday_card\/list","methods":["GET","HEAD"]},"birthday_card.create":{"uri":"birthday_card","methods":["POST"]},"birthday_card.update":{"uri":"birthday_card\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"birthday_card.delete":{"uri":"birthday_card\/{id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"training.safe":{"uri":"training\/safe","methods":["GET","HEAD"]},"training.skill":{"uri":"training\/skill","methods":["GET","HEAD"]},"training.multiple":{"uri":"training\/multiple","methods":["GET","HEAD"]},"training.list":{"uri":"training\/{type}\/list","methods":["GET","HEAD"],"wheres":{"type":"safe|skill|multiple"},"parameters":["type"]},"training.import":{"uri":"training\/{type}\/import","methods":["POST"],"wheres":{"type":"safe|skill|multiple"},"parameters":["type"]},"training.export":{"uri":"training\/{type}\/export","methods":["POST"],"wheres":{"type":"safe|skill|multiple"},"parameters":["type"]},"training.patch":{"uri":"training\/{type}\/patch","methods":["POST"],"wheres":{"type":"safe|skill|multiple"},"parameters":["type"]},"training.template":{"uri":"training\/{type}\/template","methods":["GET","HEAD"],"wheres":{"type":"safe|skill|multiple"},"parameters":["type"]},"training.batch_delete":{"uri":"training\/{type}\/delete","methods":["POST"],"wheres":{"type":"safe|skill|multiple"},"parameters":["type"]},"training.update":{"uri":"training\/{type}\/{id}","methods":["PUT"],"wheres":{"type":"safe|skill|multiple","id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["type","id"]},"training.upload":{"uri":"training\/{type}\/{id}\/upload","methods":["POST"],"wheres":{"type":"safe|skill|multiple","id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["type","id"]},"training.file_delete":{"uri":"training\/{type}\/{id}\/delete\/{file}","methods":["DELETE"],"wheres":{"type":"safe|skill|multiple","id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","file":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["type","id","file"]},"file.index":{"uri":"file","methods":["GET","HEAD"]},"file.list":{"uri":"file\/list","methods":["GET","HEAD"]},"file.create":{"uri":"file","methods":["POST"]},"file.upload":{"uri":"file\/upload","methods":["POST"]},"file.batch_delete":{"uri":"file\/batch\/delete","methods":["POST"]},"file.batch_move":{"uri":"file\/batch\/move","methods":["POST"]},"file.view":{"uri":"file\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"file.update":{"uri":"file\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"file.move":{"uri":"file\/{id}\/move","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"file.delete":{"uri":"file\/{id}\/delete","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"file.download":{"uri":"file\/{id}\/download","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"assembly.index":{"uri":"assembly","methods":["GET","HEAD"]},"assembly.list":{"uri":"assembly\/list","methods":["GET","HEAD"]},"assembly.option":{"uri":"assembly\/option","methods":["GET","HEAD"]},"assembly.create":{"uri":"assembly","methods":["POST"]},"assembly.export":{"uri":"assembly\/export","methods":["POST"]},"assembly.import":{"uri":"assembly\/import","methods":["POST"]},"assembly.template":{"uri":"assembly\/template","methods":["GET","HEAD"]},"assembly.update":{"uri":"assembly\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"assembly.delete":{"uri":"assembly\/{id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"torque.index":{"uri":"torque","methods":["GET","HEAD"]},"torque.list":{"uri":"torque\/list","methods":["GET","HEAD"]},"torque.import":{"uri":"torque\/import","methods":["POST"]},"torque.template":{"uri":"torque\/template","methods":["GET","HEAD"]},"torque.update":{"uri":"torque\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"torque_change_record.list":{"uri":"torque\/changed\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"issue.inline":{"uri":"issue","methods":["GET","HEAD"]},"issue.product":{"uri":"issue\/product","methods":["GET","HEAD"]},"issue.service":{"uri":"issue\/service","methods":["GET","HEAD"]},"issue.finish":{"uri":"issue\/finish","methods":["GET","HEAD"]},"issue.export":{"uri":"issue\/export","methods":["POST"]},"issue.detail":{"uri":"issue\/detail","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"}},"issue.update":{"uri":"issue\/update","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"}},"issue.list":{"uri":"issue\/list","methods":["GET","HEAD"]},"plan.index":{"uri":"plan","methods":["GET","HEAD"]},"plan.list":{"uri":"plan\/list","methods":["GET","HEAD"]},"plan.create":{"uri":"plan","methods":["POST"]},"plan.export":{"uri":"plan\/export","methods":["POST"]},"plan.update":{"uri":"plan\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"plan.delete":{"uri":"plan\/{id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"torque_item.index":{"uri":"spc","methods":["GET","HEAD"]},"torque_item.list":{"uri":"spc\/list","methods":["GET","HEAD"]},"torque_item.export":{"uri":"spc\/export","methods":["POST"]},"torque_item.update":{"uri":"spc\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"examine.index":{"uri":"examine","methods":["GET","HEAD"]},"examine.list":{"uri":"examine\/list","methods":["GET","HEAD"]},"examine.option":{"uri":"examine\/option\/{type}","methods":["GET","HEAD"],"wheres":{"type":"inline|product|vehicle"},"parameters":["type"]},"examine.delete":{"uri":"examine\/{id}\/{type}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","type":"inline|product|vehicle"},"parameters":["id","type"]},"commit_approve.create":{"uri":"commit\/approve","methods":["POST"]},"commit_vehicle.index":{"uri":"commit\/vehicle","methods":["GET","HEAD"]},"commit_vehicle.list":{"uri":"commit\/vehicle\/list","methods":["GET","HEAD"]},"commit_vehicle.create":{"uri":"commit\/vehicle","methods":["POST"]},"commit_vehicle.update":{"uri":"commit\/vehicle\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_vehicle.detail":{"uri":"commit\/vehicle\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_vehicle.changed":{"uri":"commit\/vehicle\/{id}\/changed","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_vehicle.approve":{"uri":"commit\/vehicle\/{id}","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_vehicle.delete":{"uri":"commit\/vehicle\/{id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_vehicle.import":{"uri":"commit\/vehicle\/import","methods":["POST"]},"commit_vehicle.template":{"uri":"commit\/vehicle\/template","methods":["GET","HEAD"]},"commit_vehicle.option":{"uri":"commit\/vehicle\/option","methods":["GET","HEAD"]},"commit_vehicle_item.list":{"uri":"commit\/vehicle\/item\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_vehicle_item.create":{"uri":"commit\/vehicle\/item\/{id}","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_vehicle_item.order":{"uri":"commit\/vehicle\/item\/{id}\/order","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_vehicle_item.upload":{"uri":"commit\/vehicle\/item\/{id}\/upload","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_vehicle_item.update":{"uri":"commit\/vehicle\/item\/{id}\/{item_id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id","item_id"]},"commit_vehicle_item.delete":{"uri":"commit\/vehicle\/item\/{id}\/{item_id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id","item_id"]},"commit_inline.index":{"uri":"commit\/inline","methods":["GET","HEAD"]},"commit_inline.list":{"uri":"commit\/inline\/list","methods":["GET","HEAD"]},"commit_inline.create":{"uri":"commit\/inline","methods":["POST"]},"commit_inline.update":{"uri":"commit\/inline\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_inline.detail":{"uri":"commit\/inline\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_inline.changed":{"uri":"commit\/inline\/{id}\/changed","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_inline.approve":{"uri":"commit\/inline\/{id}","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_inline.delete":{"uri":"commit\/inline\/{id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_inline.import":{"uri":"commit\/inline\/import","methods":["POST"]},"commit_inline.template":{"uri":"commit\/inline\/template","methods":["GET","HEAD"]},"commit_inline.option":{"uri":"commit\/inline\/option","methods":["GET","HEAD"]},"commit_inline_item.list":{"uri":"commit\/inline\/item\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_inline_item.create":{"uri":"commit\/inline\/item\/{id}","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_inline_item.order":{"uri":"commit\/inline\/item\/{id}\/order","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_inline_item.upload":{"uri":"commit\/inline\/item\/{id}\/upload","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_inline_item.update":{"uri":"commit\/inline\/item\/{id}\/{item_id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id","item_id"]},"commit_inline_item.delete":{"uri":"commit\/inline\/item\/{id}\/{item_id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id","item_id"]},"commit_inline_item.option":{"uri":"commit\/inline\/item\/{id}\/{item_id}","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id","item_id"]},"commit_product.index":{"uri":"commit\/product","methods":["GET","HEAD"]},"commit_product.list":{"uri":"commit\/product\/list","methods":["GET","HEAD"]},"commit_product.create":{"uri":"commit\/product","methods":["POST"]},"commit_product.update":{"uri":"commit\/product\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_product.detail":{"uri":"commit\/product\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_product.changed":{"uri":"commit\/product\/{id}\/changed","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_product.approve":{"uri":"commit\/product\/{id}","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_product.delete":{"uri":"commit\/product\/{id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_product.import":{"uri":"commit\/product\/import","methods":["POST"]},"commit_product.template":{"uri":"commit\/product\/template","methods":["GET","HEAD"]},"commit_product.option":{"uri":"commit\/product\/option","methods":["GET","HEAD"]},"commit_product_item.list":{"uri":"commit\/product\/item\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_product_item.create":{"uri":"commit\/product\/item\/{id}","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_product_item.order":{"uri":"commit\/product\/item\/{id}\/order","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_product_item.upload":{"uri":"commit\/product\/item\/{id}\/upload","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"commit_product_item.update":{"uri":"commit\/product\/item\/{id}\/{item_id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id","item_id"]},"commit_product_item.delete":{"uri":"commit\/product\/item\/{id}\/{item_id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id","item_id"]},"commit_product_item.option":{"uri":"commit\/product\/item\/{id}\/{item_id}","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id","item_id"]},"task.index":{"uri":"task","methods":["GET","HEAD"]},"task.list":{"uri":"task\/list","methods":["GET","HEAD"]},"task.option":{"uri":"task\/option","methods":["GET","HEAD"]},"task.create":{"uri":"task","methods":["POST"]},"task.delete":{"uri":"task\/{id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"task_cron.index":{"uri":"task\/cron","methods":["GET","HEAD"]},"task_cron.list":{"uri":"task\/cron\/list","methods":["GET","HEAD"]},"task_cron.create":{"uri":"task\/cron","methods":["POST"]},"task_cron.patch":{"uri":"task\/cron\/{id}\/{status}","methods":["PATCH"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","status":"valid|invalid"},"parameters":["id","status"]},"task_cron.delete":{"uri":"task\/cron\/{id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"work.index":{"uri":"work","methods":["GET","HEAD"]},"work.list":{"uri":"work\/list","methods":["GET","HEAD"]},"work.create":{"uri":"work","methods":["POST"]},"notice.index":{"uri":"message","methods":["GET","HEAD"]},"notice.list":{"uri":"message\/list","methods":["GET","HEAD"]},"notice.create":{"uri":"message","methods":["POST"]},"notice.detail":{"uri":"message\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"notice.update":{"uri":"message\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"notice.batch_delete":{"uri":"message\/delete","methods":["POST"]},"notice.retract":{"uri":"message\/retract","methods":["POST"]},"notice.push":{"uri":"message\/push","methods":["POST"]},"document.overhaul":{"uri":"document\/overhaul","methods":["GET","HEAD"]},"document.assembling":{"uri":"document\/assembling","methods":["GET","HEAD"]},"document.torque":{"uri":"document\/torque","methods":["GET","HEAD"]},"document.list":{"uri":"document\/list\/{type}","methods":["GET","HEAD"],"wheres":{"type":"[0-9]+"},"parameters":["type"]},"document.overhaul_update":{"uri":"document\/overhaul\/{engine}","methods":["POST"],"wheres":{"engine":"[0-9]+"},"parameters":["engine"]},"document.assembling_update":{"uri":"document\/assembling\/{engine}","methods":["POST"],"wheres":{"engine":"[0-9]+"},"parameters":["engine"]},"document.torque_update":{"uri":"document\/torque\/{engine}","methods":["POST"],"wheres":{"engine":"[0-9]+"},"parameters":["engine"]},"document.delete":{"uri":"document\/{id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"product.index":{"uri":"product","methods":["GET","HEAD"]},"product.list":{"uri":"product\/list","methods":["GET","HEAD"]},"product.create":{"uri":"product","methods":["POST"]},"product.update":{"uri":"product\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"product.delete":{"uri":"product\/{id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"product.export":{"uri":"product\/export","methods":["POST"]},"product.import":{"uri":"product\/import","methods":["POST"]},"product.template":{"uri":"product\/template","methods":["GET","HEAD"]},"part.index":{"uri":"part","methods":["GET","HEAD"]},"part.list":{"uri":"part\/list","methods":["GET","HEAD"]},"part.create":{"uri":"part","methods":["POST"]},"part.update":{"uri":"part\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"part.delete":{"uri":"part\/{id}","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"part.item":{"uri":"part\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"part.import":{"uri":"part\/import","methods":["POST"]},"part.template":{"uri":"part\/template","methods":["GET","HEAD"]},"vehicle.index":{"uri":"vehicle","methods":["GET","HEAD"]},"vehicle.finish":{"uri":"vehicle\/finish","methods":["GET","HEAD"]},"vehicle.task":{"uri":"vehicle\/task","methods":["GET","HEAD"]},"vehicle.task_list":{"uri":"vehicle\/task\/list","methods":["GET","HEAD"]},"vehicle.task_detail":{"uri":"vehicle\/task\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"vehicle.task_edit":{"uri":"vehicle\/task\/{id}\/edit","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"vehicle.task_upload":{"uri":"vehicle\/task\/{id}\/upload","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"vehicle.task_delete":{"uri":"vehicle\/task\/{id}\/delete","methods":["DELETE"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"vehicle.list":{"uri":"vehicle\/list","methods":["GET","HEAD"]},"vehicle.upload":{"uri":"vehicle\/upload\/{id}","methods":["POST"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"vehicle.detail":{"uri":"vehicle\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"vehicle.report":{"uri":"vehicle\/report\/{id}","methods":["GET","HEAD"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"vehicle.update":{"uri":"vehicle\/{id}","methods":["PUT"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"vehicle.close":{"uri":"vehicle\/{id}","methods":["PATCH"],"wheres":{"id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},"parameters":["id"]},"report.vehicle":{"uri":"report\/vehicle","methods":["GET","HEAD"]},"report.product":{"uri":"report\/product","methods":["GET","HEAD"]},"report.inline":{"uri":"report\/inline","methods":["GET","HEAD"]},"report.vehicle_daily":{"uri":"report\/vehicle\/daily","methods":["GET","HEAD"]},"report.product_daily":{"uri":"report\/product\/daily","methods":["GET","HEAD"]},"report.inline_daily":{"uri":"report\/inline\/daily","methods":["GET","HEAD"]},"report.vehicle_weekly":{"uri":"report\/vehicle\/weekly","methods":["GET","HEAD"]},"report.product_weekly":{"uri":"report\/product\/weekly","methods":["GET","HEAD"]},"report.inline_weekly":{"uri":"report\/inline\/weekly","methods":["GET","HEAD"]},"report.vehicle_monthly":{"uri":"report\/vehicle\/monthly","methods":["GET","HEAD"]},"report.product_monthly":{"uri":"report\/product\/monthly","methods":["GET","HEAD"]},"report.inline_monthly":{"uri":"report\/inline\/monthly","methods":["GET","HEAD"]},"report.product_yearly":{"uri":"report\/product\/yearly","methods":["GET","HEAD"]},"report.inline_yearly":{"uri":"report\/inline\/yearly","methods":["GET","HEAD"]},"custom.export":{"uri":"report\/custom","methods":["POST"]}}};

if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
