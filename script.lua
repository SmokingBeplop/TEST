-- Settings
local HoldClick = true
local Hotkey = "x"
local HotkeyToggle = true
local TeamCheck = true -- ignore players on your team

-- BASE DELAY SETTINGS
local MinDelay = 0.05   -- fastest possible delay
local MaxDelay = 0.20   -- slowest delay
local Sensitivity = 0.002 -- how much flick speed affects delay

local ScopedIn = false
local ScopeDelayTime = 0

local Players = game:GetService("Players")
local RunService = game:GetService("RunService")
local UserInputService = game:GetService("UserInputService")

local LocalPlayer = Players.LocalPlayer
local Mouse = LocalPlayer:GetMouse()

local Enabled = false
local RightClickHeld = false
local CurrentlyPressed = false

-- Flick tracking
local LastMousePos = Vector2.new(0, 0)
local FlickSpeed = 0

-- FIXED: Combined target validation (works with BOTH versions' logic)
local function isValidTarget(target)
	if not target then return false end
	
	-- Original Version 1 logic (fast)
	local humanoid1 = target.Parent:FindFirstChild("Humanoid")
	if humanoid1 then
		return true
	end
	
	-- Enhanced Version 2 logic (safer)
	local character = target:FindFirstAncestorOfClass("Model")
	if character then
		local humanoid2 = character:FindFirstChildOfClass("Humanoid")
		if humanoid2 and humanoid2.Health > 0 then
			local targetPlayer = Players:GetPlayerFromCharacter(character)
			if targetPlayer and targetPlayer ~= LocalPlayer then
				if not TeamCheck or 
				   (targetPlayer.Team ~= LocalPlayer.Team and targetPlayer.TeamColor ~= LocalPlayer.TeamColor) then
					return true
				end
			else
				-- Non-player humanoid (NPCs) - allow if no team check
				return not TeamCheck
			end
		end
	end
	
	return false
end

Mouse.KeyDown:Connect(function(key)
	key = key:lower()
	if key == Hotkey:lower() then
		if HotkeyToggle then
			Enabled = not Enabled
			print("Autotrigger:", Enabled and "ON" or "OFF")
		else
			Enabled = true
		end
	end
end)

Mouse.KeyUp:Connect(function(key)
	key = key:lower()
	if not HotkeyToggle and key == Hotkey:lower() then
		Enabled = false
	end
end)

UserInputService.InputBegan:Connect(function(input, gameProcessed)
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

RunService.RenderStepped:Connect(function(dt)
	-- Calculate flick speed
	local currentPos = UserInputService:GetMouseLocation()
	local delta = (currentPos - LastMousePos).Magnitude
	FlickSpeed = delta / (dt > 0 and dt or 1)
	LastMousePos = currentPos

	-- Convert flick speed into delay
	local DynamicDelay = math.clamp(
		MaxDelay - (FlickSpeed * Sensitivity),
		MinDelay,
		MaxDelay
	)

	if Enabled and RightClickHeld and ScopedIn then
		local DelayPassed = (tick() - ScopeDelayTime) >= DynamicDelay
		
		-- FIXED: Uses combined validation - works on ALL targets
		if Mouse.Target and isValidTarget(Mouse.Target) and DelayPassed then
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
