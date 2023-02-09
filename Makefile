humaid_build_prod:
	docker build  --file ./docker/cljs/Dockerfile --tag mks_humaid_app ./shared/humaid/
