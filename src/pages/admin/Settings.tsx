import { Layout } from "@/components/courier/Layout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Switch } from "@/components/ui/switch";
import { Textarea } from "@/components/ui/textarea";
import { Separator } from "@/components/ui/separator";

const Settings = () => {
  return (
    <Layout userRole="admin" userName="John Smith" userEmail="admin@courierpro.com">
      <div className="p-6">
        <div className="mb-6">
          <h1 className="text-2xl font-bold text-foreground">Settings</h1>
          <p className="text-muted-foreground">Manage system settings and preferences</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card>
            <CardHeader>
              <CardTitle>Company Information</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <Label htmlFor="companyName">Company Name</Label>
                <Input id="companyName" defaultValue="CourierPro Ltd." />
              </div>
              <div>
                <Label htmlFor="companyEmail">Company Email</Label>
                <Input id="companyEmail" type="email" defaultValue="contact@courierpro.com" />
              </div>
              <div>
                <Label htmlFor="companyPhone">Phone Number</Label>
                <Input id="companyPhone" defaultValue="+1-555-0100" />
              </div>
              <div>
                <Label htmlFor="companyAddress">Address</Label>
                <Textarea id="companyAddress" defaultValue="123 Business Street, Corporate City, State 12345" />
              </div>
              <Button>Save Changes</Button>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>System Preferences</CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="flex items-center justify-between">
                <div className="space-y-0.5">
                  <Label>Email Notifications</Label>
                  <p className="text-sm text-muted-foreground">Send email notifications for important events</p>
                </div>
                <Switch defaultChecked />
              </div>
              
              <Separator />
              
              <div className="flex items-center justify-between">
                <div className="space-y-0.5">
                  <Label>SMS Notifications</Label>
                  <p className="text-sm text-muted-foreground">Send SMS updates to customers</p>
                </div>
                <Switch defaultChecked />
              </div>
              
              <Separator />
              
              <div className="flex items-center justify-between">
                <div className="space-y-0.5">
                  <Label>Auto Status Updates</Label>
                  <p className="text-sm text-muted-foreground">Automatically update courier status</p>
                </div>
                <Switch />
              </div>
              
              <Separator />
              
              <div className="flex items-center justify-between">
                <div className="space-y-0.5">
                  <Label>Customer Portal</Label>
                  <p className="text-sm text-muted-foreground">Enable customer self-service portal</p>
                </div>
                <Switch defaultChecked />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Security Settings</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <Label htmlFor="currentPassword">Current Password</Label>
                <Input id="currentPassword" type="password" />
              </div>
              <div>
                <Label htmlFor="newPassword">New Password</Label>
                <Input id="newPassword" type="password" />
              </div>
              <div>
                <Label htmlFor="confirmPassword">Confirm New Password</Label>
                <Input id="confirmPassword" type="password" />
              </div>
              <Button>Update Password</Button>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Delivery Settings</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <Label htmlFor="defaultDeliveryTime">Default Delivery Time (hours)</Label>
                <Input id="defaultDeliveryTime" type="number" defaultValue="48" />
              </div>
              <div>
                <Label htmlFor="maxDeliveryTime">Maximum Delivery Time (hours)</Label>
                <Input id="maxDeliveryTime" type="number" defaultValue="168" />
              </div>
              <div>
                <Label htmlFor="baseDeliveryFee">Base Delivery Fee ($)</Label>
                <Input id="baseDeliveryFee" type="number" step="0.01" defaultValue="10.00" />
              </div>
              <div>
                <Label htmlFor="perKmRate">Per KM Rate ($)</Label>
                <Input id="perKmRate" type="number" step="0.01" defaultValue="1.50" />
              </div>
              <Button>Save Settings</Button>
            </CardContent>
          </Card>
        </div>
      </div>
    </Layout>
  );
};

export default Settings;