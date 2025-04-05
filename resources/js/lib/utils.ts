import type { Updater } from '@tanstack/vue-table'
import type { Ref } from 'vue'
import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function valueUpdater<T extends Updater<any>>(updaterOrValue: T, ref: Ref) {
  ref.value
    = typeof updaterOrValue === 'function'
      ? updaterOrValue(ref.value)
      : updaterOrValue
}


// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
export function broadcast(channel: string, event: string, callback: Function) {
    return window.Echo.channel(channel).listen(event, (e: any) => {
                callback(e)
          })
}

// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
export function broadcastOnPrivate(channel: string, event: string, callback: Function) {
    return window.Echo.private(channel).listen(event, (e: any) => {
                callback(e)
            })
}


export function stopListeningOnChannelEvent(channel:string, event: string) {
    window.Echo.private(channel).stopListening(event)
}
