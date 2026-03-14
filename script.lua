-- Settings
local HoldClick = true
local HotkeyToggle = true

-- SCOPE DELAY SETTINGS
local ScopeDelay = 0.15 -- Delay in seconds after scoping (0 = instant)
local ScopedIn = false
local ScopeDelayTime = 0

local Players = game:GetService("Players")
local RunService = game:GetService("RunService")
local UserInputService = game:GetService("UserInputService")

local LocalPlayer = Players.LocalPlayer
local Mouse = LocalPlayer:GetMouse()

local Enabled = true  -- AUTO-TRIGGER ENABLED BY DEFAULT
local RightClickHeld = false
local CurrentlyPressed = false

-- LEFT ALT TOGGLE (Fixed!)
UserInputService.InputBegan:Connect(function(input, gameProcessed)
	if gameProcessed then return end
	
	-- Left Alt toggle
	if input.KeyCode == Enum.KeyCode.LeftAlt then
		if HotkeyToggle then
			Enabled = not Enabled
			print("Autotrigger:", Enabled and "ON" or "OFF")
		else
			Enabled = true
		end
	end
	
	-- Right click scope
	if input.UserInputType == Enum.UserInputType.MouseButton2 then
		RightClickHeld = true
		ScopedIn = true
		ScopeDelayTime = tick()
	end
end)

UserInputService.InputEnded:Connect(function(input, gameProcessed)
	if input.UserInputType == Enum.UserInputType.MouseButton2 then
		RightClickHeld = false
		ScopedIn = false

		if HoldClick and CurrentlyPressed then
			CurrentlyPressed = false
			mouse1release()
		end
	end
end)

RunService.RenderStepped:Connect(function()
	if Enabled and RightClickHeld and ScopedIn then
		local DelayPassed = (tick() - ScopeDelayTime) >= ScopeDelay
		
		if Mouse.Target and Mouse.Target.Parent:FindFirstChild("Humanoid") and DelayPassed then
			if HoldClick then
				if not CurrentlyPressed then
					CurrentlyPressed = true
					mouse1press()
				end
			else
				mouse1click()
			end
		else
			if HoldClick and CurrentlyPressed then
				CurrentlyPressed = false
				mouse1release()
			end
		end
	else
		if HoldClick and CurrentlyPressed then
			CurrentlyPressed = false
			mouse1release()
		end
	end
end)

print("Scope Delay Trigger FIXED - Left Alt to toggle (ON by default)")
