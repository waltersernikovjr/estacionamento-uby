import { useState } from "react";
import { RegisterClienteForm } from "../components/forms/RegisterClienteForm";
import { RegisterOperadorForm } from "../components/forms/RegisterOperadorForm";
import { LoginOperadorForm } from "../components/forms/LoginOperadorForm";
import { LoginClienteForm } from "../components/forms/LoginClienteForm";

export const Singin = () => {
    const [toggle, setToggle] = useState<boolean>(false)
    const [register, setRegister] = useState<boolean>(false)

    return <div className="w-2/4 h-10/12 rounded-md shadow-2xl bg-gray-950">
        <div className="flex items-center gap-4 p-10">
            <span className="text-sm font-medium text-gray-700">
            </span>
            <button
                onClick={() => setToggle(!toggle)}
                className={`
          relative inline-flex h-6 w-11 items-center rounded-full transition-colors
          ${toggle ? "bg-blue-600" : "bg-gray-300"}
        `}
            >
                <span
                    className={`
            inline-block h-4 w-4 transform rounded-full bg-white transition-transform
            ${toggle ? "translate-x-6" : "translate-x-1"}
          `}
                />
            </button>
        </div>

        <div className="flex flex-col items-center ">
            {
                register ?
                    toggle ? <RegisterOperadorForm /> : <RegisterClienteForm />
                    : toggle ? <LoginOperadorForm /> : <LoginClienteForm />
            }
            {
                !register ?
                    <button onClick={() => setRegister(true)}><p className="cursor-pointer underline m-5 mb-10">Se nao tiver conta registre-se aqui</p></button>
                    : <></>
            }
        </div>
    </div>


}