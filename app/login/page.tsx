import { LoginForm } from "@/components/auth/login-form"
import Image from "next/image"

export default function LoginPage() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-background via-muted/30 to-background p-4">
      <div className="w-full max-w-md">
        <div className="flex justify-center mb-8">
          <div className="text-center">
            <div className="inline-flex items-center justify-center mb-4">
              <Image
                src="/sip-abacus-logo.png"
                alt="SIP Academy Logo"
                width={200}
                height={80}
                className="drop-shadow-lg"
                priority
              />
            </div>
            <p className="text-sm text-muted-foreground mt-1">Alumni Admin Portal</p>
          </div>
        </div>

        {/* Login Form */}
        <LoginForm />
      </div>
    </div>
  )
}
