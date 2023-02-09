(ns humaid.subs
  (:require [re-frame.core :refer [reg-sub]]))

(reg-sub
 :current-route
 (fn [db _]
   (:current-route db)))

(reg-sub
 :path-params
 (fn [db _]
   (-> db :current-route :parameters :path)))

(reg-sub
 :stopwatch-val
 (fn [db _]
   (-> db :stopwatch :val)))

(reg-sub
 :search
 (fn [db _] (:search db)))

(reg-sub
 :loading
 (fn [db _] (:loading db)))

(reg-sub
 :client
 (fn [db _] (:client db)))

(reg-sub
 :client-deliveries
 (fn [db _] (:client-deliveries db)))

(reg-sub
 :client-services
 (fn [db _] (:client-services db)))

(reg-sub
 :delivery-items
 (fn [db _] (:delivery-items db)))

(reg-sub
 :notification
 (fn [db _] (:notification db)))

(reg-sub
 :branch-items
 (fn [db _] (:branch-items db)))