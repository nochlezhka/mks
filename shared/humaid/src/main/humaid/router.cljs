(ns humaid.router
  (:require [humaid.views :as views]
            [re-frame.core :refer [dispatch]]
            [reitit.coercion.spec :as rss]
            [reitit.frontend :as rf]
            [reitit.frontend.easy :as rfe]))

(def routes
  ["/"
   {:controllers
    [{:start #(dispatch [:delivery-items-get])}]}

   [""
    {:name :search
     :view views/search-page
     :controllers
     [{:start (fn []
                (dispatch [:clear-search])
                (dispatch [:reset-stopwatch]))}]}]

   ["clients/:client-id"
    {:name :client
     :view views/client-page
     :controllers
     [{:parameters {:path [:client-id]}
       :start (fn [{{:keys [client-id]} :path}]
                (dispatch [:start-stopwatch])
                (dispatch [:client-get client-id]))}]}]

   ["clients/:client-id/delivery/:delivery-items-kind"
    {:name :delivery
     :view views/delivery-page
     :controllers
     [{:parameters {:path [:client-id]}
       :start (fn [{{:keys [client-id]} :path}]
                (dispatch [:start-stopwatch])
                (dispatch [:client-get client-id])
                (dispatch [:client-get-deliveries client-id])
                (dispatch [:client-get-services client-id]))}]}]

   ["not-found"
    {:name :not-found
     :view views/not-found-page}]])

(def router
  (rf/router
   routes
   {:data {:coercion rss/coercion}}))

(defn on-navigate [new-match]
  (if new-match
    (dispatch [:navigated new-match])
    (dispatch [:push-state :not-found])))

(defn init-routes! []
  (rfe/start!
   router
   on-navigate
   {:use-fragment true}))
