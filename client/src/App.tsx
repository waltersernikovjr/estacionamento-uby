import { useEffect, useState } from "react"

import type { UserProps } from "./model/User";
import { Singin } from "./components/Singin";

function App() {
  const [user, setUser] = useState<UserProps | null>(null);

  useEffect(() => {
    //GetCookie
  }, [])

  return (
    <>
      <div>
        <div className="h-20 bg-blue-950">

        </div>
        {!user ? <div className="flex justify-evenly p-4"><Singin /></div> : <></>}
      </div>
    </>
  )
}

export default App
