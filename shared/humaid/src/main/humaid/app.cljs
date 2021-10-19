(ns humaid.app
  (:require
   [ajax.core :as ajax]
   [ajax.protocols]
   [humaid.events]
   [humaid.router]
   [humaid.subs]
   [humaid.views]
   [reagent.dom :as rdom]))

(def auth-interceptor
  (ajax/to-interceptor
   {:name "Auth interceptor"
    :response
    (fn [response]
      (if (= 401 (ajax.protocols/-status response))
        (do
          (let [login-addr  "/login?_target_path=/humaid/"]
            (set! (.-href js/window.location) login-addr))
          (reduced [0 nil]))
        response
        ))}))

(defn mount-components []
  (rdom/render [#'humaid.views/notification] (.getElementById js/document "notification"))
  (rdom/render [humaid.views/page {:router humaid.router/router}]
               (.getElementById js/document "app")))

(defn init! []
  (swap! ajax/default-interceptors concat [auth-interceptor])
  (humaid.router/init-routes!)
  (mount-components))
