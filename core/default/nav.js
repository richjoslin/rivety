{
	"frontend":{
		"children":{
			"main_home":{
				"label":"Home",
				"url":"\/"
			}
		}
	},
	"admin":{
		"children":{
			"admin_dashboard":{
				"label":"Dashboard",
				"url":"\/default\/admin\/index"
			},
			"admin_useradmin":{
				"label":"Users",
				"url":"\/default\/useradmin\/index"
			},
			"admin_settings":{
				"label":"Settings",
				"children":{
					"admin_config":{
						"label":"Config",
						"url":"\/default\/config\/index"
					},
					"admin_modules":{
						"label":"Modules",
						"url":"\/default\/module\/index"
					},
					"admin_roles":{
						"label":"Roles",
						"url":"\/default\/role\/index"
					},
					"admin_testdata":{
						"label":"Load Test Data",
						"url":"\/default\/useradmin\/testdata"
					}
				}
			}
		}
	}
}
